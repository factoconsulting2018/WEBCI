<?php

use yii\db\Migration;

/**
 * Handles the creation of tables for benefits and benefit categories.
 */
class m241115_000010_create_benefit_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%benefit_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(180)->notNull(),
            'description' => $this->text(),
            'logo' => $this->string(120),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-benefit_category-sort', '{{%benefit_category}}', ['is_active', 'sort_order']);

        $this->createTable('{{%benefit}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'logo' => $this->string(120),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-benefit-category', '{{%benefit}}', 'category_id');
        $this->createIndex('idx-benefit-sort', '{{%benefit}}', ['is_active', 'sort_order']);

        $this->addForeignKey(
            'fk-benefit-category',
            '{{%benefit}}',
            'category_id',
            '{{%benefit_category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $timestamp = time();
        $this->batchInsert('{{%benefit_category}}', ['name', 'description', 'logo', 'sort_order', 'is_active', 'created_at', 'updated_at'], [
            ['Servicios Financieros y Tributarios', 'Primer categoría de beneficios según lineamientos de noviembre 2024.', 'finanzas', 10, 1, $timestamp, $timestamp],
        ]);

        $categoryId = (new \yii\db\Query())
            ->select('id')
            ->from('{{%benefit_category}}')
            ->where(['name' => 'Servicios Financieros y Tributarios'])
            ->scalar();

        if ($categoryId) {
            $this->batchInsert('{{%benefit}}', ['category_id', 'title', 'description', 'logo', 'sort_order', 'is_active', 'created_at', 'updated_at'], [
                [$categoryId, 'Asesoría para su mejor financiamiento en diversas entidades debidamente inscritas ante SUGEF.', null, 'finanzas', 10, 1, $timestamp, $timestamp],
                [$categoryId, 'Servicios de presentación de IVA y renta. Contabilidad.', null, 'impuestos', 20, 1, $timestamp, $timestamp],
                [$categoryId, 'Asesoría Tributaria.', null, 'impuestos', 30, 1, $timestamp, $timestamp],
                [$categoryId, 'Acceso a nuestro programa de microcréditos*.', null, 'microcreditos', 40, 1, $timestamp, $timestamp],
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-benefit-category', '{{%benefit}}');
        $this->dropTable('{{%benefit}}');
        $this->dropTable('{{%benefit_category}}');
    }
}

