<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sponsor_set}}`.
 */
class m241114_000006_create_sponsor_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%sponsor_set}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(120)->defaultValue('Patrocinadores'),
            'image_one' => $this->string(255),
            'image_two' => $this->string(255),
            'image_three' => $this->string(255),
            'image_four' => $this->string(255),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->insert('{{%sponsor_set}}', [
            'title' => 'Patrocinadores',
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sponsor_set}}');
    }
}

