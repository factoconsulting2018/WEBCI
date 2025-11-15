<?php

use yii\db\Migration;

class m241115_000020_add_available_in_search_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%business}}',
            'available_in_search',
            $this->boolean()->notNull()->defaultValue(true)->after('show_on_home')
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%business}}', 'available_in_search');
    }
}


