<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m241114_000001_create_category_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'slug' => $this->string(160)->notNull()->unique(),
            'description' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}

