<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%business}}`.
 */
class m241114_000003_create_business_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%business}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(160)->notNull(),
            'slug' => $this->string(180)->notNull()->unique(),
            'summary' => $this->string(255),
            'description' => $this->text(),
            'whatsapp' => $this->string(32),
            'address' => $this->string(255),
            'email' => $this->string(180)->notNull(),
            'social_links' => $this->text(),
            'logo_path' => $this->string(255),
            'show_on_home' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'email_template_id' => $this->integer()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-business-name', '{{%business}}', 'name');
        $this->createIndex('idx-business-email', '{{%business}}', 'email', true);
        $this->createIndex('idx-business-show_on_home', '{{%business}}', 'show_on_home');

        $this->addForeignKey(
            'fk-business-email_template_id',
            '{{%business}}',
            'email_template_id',
            '{{%email_template}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-business-email_template_id', '{{%business}}');
        $this->dropTable('{{%business}}');
    }
}

