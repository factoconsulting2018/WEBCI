<?php

use yii\db\Migration;

class m241114_000008_create_site_config_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%site_config}}', [
            'id' => $this->primaryKey(),
            'logo_path' => $this->string(255),
            'logo_width' => $this->integer(),
            'logo_height' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->insert('{{%site_config}}', [
            'id' => 1,
            'logo_path' => null,
            'logo_width' => null,
            'logo_height' => null,
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%site_config}}');
    }
}

