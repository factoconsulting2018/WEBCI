<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $business_id
 * @property string $fullname
 * @property string $phone
 * @property string $address
 * @property string $subject
 * @property int $created_at
 *
 * @property-read Business $business
 */
class ContactSubmission extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%contact_submission}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['business_id', 'fullname', 'phone', 'address', 'subject'], 'required'],
            [['business_id'], 'integer'],
            [['fullname'], 'string', 'max' => 160],
            [['phone'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 255],
            [['subject'], 'string', 'max' => 180],
            [['business_id'], 'exist', 'targetClass' => Business::class, 'targetAttribute' => ['business_id' => 'id']],
            [['fullname', 'phone', 'address', 'subject'], 'filter', 'filter' => fn($value) => $this->toUpper($value)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'business_id' => 'Aliado',
            'fullname' => 'Nombre completo',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'subject' => 'Asunto',
            'created_at' => 'Creado',
        ];
    }

    public function getBusiness(): ActiveQuery
    {
        return $this->hasOne(Business::class, ['id' => 'business_id']);
    }

    private function toUpper($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);
        return $value === '' ? '' : mb_strtoupper($value);
    }
}

