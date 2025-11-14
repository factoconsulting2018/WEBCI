<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contact_submission}}`.
 */
class m241114_000005_create_contact_submission_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact_submission}}', [
            'id' => $this->primaryKey(),
            'business_id' => $this->integer()->notNull(),
            'fullname' => $this->string(160)->notNull(),
            'phone' => $this->string(64)->notNull(),
            'address' => $this->string(255)->notNull(),
            'subject' => $this->string(180)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-contact_submission-business_id', '{{%contact_submission}}', 'business_id');

        $this->addForeignKey(
            'fk-contact_submission-business_id',
            '{{%contact_submission}}',
            'business_id',
            '{{%business}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-contact_submission-business_id', '{{%contact_submission}}');
        $this->dropTable('{{%contact_submission}}');
    }
}

