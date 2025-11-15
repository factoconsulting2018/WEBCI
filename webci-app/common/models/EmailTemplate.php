<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $html_body
 * @property bool $is_default
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read Business[] $businesses
 */
class EmailTemplate extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%email_template}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'subject', 'html_body'], 'required'],
            [['html_body'], 'string'],
            [['is_default'], 'boolean'],
            [['name'], 'string', 'max' => 120],
            [['subject'], 'string', 'max' => 180],
            [['is_default'], 'default', 'value' => false],
            [['name', 'subject'], 'filter', 'filter' => fn($value) => $this->toUpper($value)],
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->is_default) {
            $condition = $this->isNewRecord ? [] : ['not', ['id' => $this->id]];
            static::updateAll(['is_default' => false], $condition);
        }

        return true;
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'subject' => 'Asunto',
            'html_body' => 'Contenido HTML',
            'is_default' => 'Predeterminada',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    public function getBusinesses(): ActiveQuery
    {
        return $this->hasMany(Business::class, ['email_template_id' => 'id']);
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

