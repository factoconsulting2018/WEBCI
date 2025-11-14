<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%business_category}}` which links businesses with categories.
 */
class m241114_000004_create_business_category_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%business_category}}', [
            'business_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-business_category', '{{%business_category}}', ['business_id', 'category_id']);

        $this->addForeignKey(
            'fk-business_category-business_id',
            '{{%business_category}}',
            'business_id',
            '{{%business}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-business_category-category_id',
            '{{%business_category}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-business_category-category_id', '{{%business_category}}');
        $this->dropForeignKey('fk-business_category-business_id', '{{%business_category}}');
        $this->dropTable('{{%business_category}}');
    }
}

