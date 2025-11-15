<?php

use Yii;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Handles the creation of table `{{%admin_user}}` and seeds the first administrator.
 */
class m241114_000007_create_admin_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%admin_user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(64),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login_at' => $this->integer(),
        ]);

        $security = Yii::$app->security;
        $now = time();

        $this->insert('{{%admin_user}}', [
            'username' => 'ronald',
            'password_hash' => $security->generatePasswordHash('Ardillita60+'),
            'auth_key' => $security->generateRandomString(),
            'access_token' => $security->generateRandomString(64),
            'status' => 10,
            'created_at' => $now,
            'updated_at' => $now,
            'last_login_at' => null,
        ]);

        Console::output('Administrator `ronald` seeded with the provided password.');
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_user}}');
    }
}

