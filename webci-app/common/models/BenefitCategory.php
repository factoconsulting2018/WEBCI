<?php

namespace common\models;

use common\services\LogoCatalog;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $logo
 * @property int $sort_order
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Benefit[] $benefits
 */
class BenefitCategory extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%benefit_category}}';
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
            [['name'], 'required'],
            [['description'], 'string'],
            [['sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['name'], 'string', 'max' => 180],
            [['logo'], 'string', 'max' => 120],
            [
                ['logo'],
                'in',
                'range' => LogoCatalog::keys(),
                'allowArray' => false,
                'skipOnEmpty' => true,
                'message' => 'Selecciona un logo del catálogo disponible.',
            ],
            [['sort_order'], 'default', 'value' => 0],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'logo' => 'Logo',
            'sort_order' => 'Orden',
            'is_active' => 'Activo',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    public function getBenefits(): ActiveQuery
    {
        return $this->hasMany(Benefit::class, ['category_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public static function getList(): array
    {
        return self::find()
            ->select('name')
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }
}

