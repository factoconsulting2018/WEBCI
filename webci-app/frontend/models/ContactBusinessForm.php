<?php

namespace frontend\models;

use common\models\Business;
use common\models\ContactSubmission;
use common\services\EmailTemplateService;
use Yii;
use yii\base\Model;

class ContactBusinessForm extends Model
{
    public ?Business $business = null;

    public int $business_id;
    public string $fullname = '';
    public string $phone = '';
    public string $address = '';
    public string $subject = '';

    public function rules(): array
    {
        return [
            [['business_id', 'fullname', 'phone', 'address', 'subject'], 'required'],
            [['business_id'], 'integer'],
            [['fullname'], 'string', 'max' => 160],
            [['phone'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 255],
            [['subject'], 'string', 'max' => 180],
            [['business_id'], 'validateBusiness'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'fullname' => 'Nombre completo',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'subject' => 'Asunto',
        ];
    }

    public function validateBusiness(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $business = Business::find()
            ->where(['id' => $this->business_id, 'is_active' => true])
            ->one();

        if (!$business) {
            $this->addError($attribute, 'El comercio solicitado no está disponible.');
            return;
        }

        $this->business = $business;
    }

    public function submit(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $business = $this->business;
        if (!$business) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $submission = new ContactSubmission([
                'business_id' => $business->id,
                'fullname' => $this->fullname,
                'phone' => $this->phone,
                'address' => $this->address,
                'subject' => $this->subject,
            ]);

            if (!$submission->save()) {
                $this->addErrors($submission->errors);
                $transaction->rollBack();
                return false;
            }

            /** @var EmailTemplateService $templateService */
            $templateService = Yii::$app->get('emailTemplateService');
            $template = $templateService->resolveTemplateForBusiness($business);
            [$subject, $body] = $templateService->render($template, [
                'businessName' => $business->name,
                'fullName' => $this->fullname,
                'phone' => $this->phone,
                'address' => $this->address,
                'subject' => $this->subject,
            ]);

            $mailer = Yii::$app->mailer->compose()
                ->setTo($business->email)
                ->setSubject($subject)
                ->setHtmlBody($body);

            if (!empty(Yii::$app->params['senderEmail'])) {
                $mailer->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName'] ?? 'WebCI']);
            }

            if (!$mailer->send()) {
                $this->addError('business_id', 'No se pudo enviar el correo. Intenta de nuevo más tarde.');
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            Yii::error($exception->getMessage(), __METHOD__);
            $this->addError('business_id', 'Ocurrió un error inesperado.');
            return false;
        }
    }
}

