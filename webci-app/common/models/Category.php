<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read Business[] $businesses
 */
class Category extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%category}}';
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
            [['name', 'slug'], 'string', 'max' => 160],
            [['description'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['slug'], 'default', 'value' => fn () => Inflector::slug($this->name)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'slug' => 'Slug',
            'description' => 'Descripción',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    public function getBusinesses(): ActiveQuery
    {
        return $this->hasMany(Business::class, ['id' => 'business_id'])
            ->viaTable('{{%business_category}}', ['category_id' => 'id']);
    }
}

