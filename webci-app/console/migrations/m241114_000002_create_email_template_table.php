<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email_template}}`.
 */
class m241114_000002_create_email_template_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'subject' => $this->string(180)->notNull(),
            'html_body' => $this->text()->notNull(),
            'is_default' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-email_template-name', '{{%email_template}}', 'name');
        $this->createIndex('idx-email_template-is_default', '{{%email_template}}', 'is_default');

        $now = time();
        $this->insert('{{%email_template}}', [
            'name' => 'Plantilla básica',
            'subject' => 'Nuevo contacto desde el portal',
            'html_body' => '<p>Hola {{businessName}},</p><p>{{fullName}} desea ponerse en contacto contigo.</p><ul><li>Teléfono: {{phone}}</li><li>Dirección: {{address}}</li><li>Asunto: {{subject}}</li></ul><p>Equipo WebCI</p>',
            'is_default' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%email_template}}');
    }
}

