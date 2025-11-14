<?php

namespace common\services;

use common\models\Business;
use common\models\EmailTemplate;
use yii\base\Component;
use yii\base\InvalidConfigException;

class EmailTemplateService extends Component
{
    public const PLACEHOLDERS = [
        'businessName',
        'fullName',
        'phone',
        'address',
        'subject',
    ];

    public function render(EmailTemplate $template, array $context): array
    {
        $replacements = [];
        foreach (self::PLACEHOLDERS as $placeholder) {
            $replacements['{{' . $placeholder . '}}'] = $context[$placeholder] ?? '';
        }

        $subject = strtr($template->subject, $replacements);
        $body = strtr($template->html_body, $replacements);

        return [$subject, $body];
    }

    public function resolveTemplateForBusiness(?Business $business): EmailTemplate
    {
        if ($business && $business->emailTemplate) {
            return $business->emailTemplate;
        }

        $default = EmailTemplate::find()->where(['is_default' => true])->orderBy(['id' => SORT_ASC])->one();
        if ($default) {
            return $default;
        }

        $first = EmailTemplate::find()->orderBy(['id' => SORT_ASC])->one();
        if ($first) {
            return $first;
        }

        throw new InvalidConfigException('No hay plantillas de email configuradas.');
    }
}

