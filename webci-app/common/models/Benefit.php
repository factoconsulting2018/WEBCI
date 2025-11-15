<?php

namespace common\models;

use common\services\LogoCatalog;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string|null $description
 * @property string|null $logo
 * @property int $sort_order
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BenefitCategory $category
 */
class Benefit extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%benefit}}';
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
            [['category_id', 'title'], 'required'],
            [['category_id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['is_active'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['logo'], 'string', 'max' => 120],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => BenefitCategory::class,
                'targetAttribute' => ['category_id' => 'id'],
            ],
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
            'category_id' => 'Categoría',
            'title' => 'Título del beneficio',
            'description' => 'Descripción',
            'logo' => 'Logo',
            'sort_order' => 'Orden',
            'is_active' => 'Activo',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(BenefitCategory::class, ['id' => 'category_id']);
    }
}

