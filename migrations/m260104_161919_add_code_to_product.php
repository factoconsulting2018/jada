<?php

use yii\db\Migration;

/**
 * Class m260104_161919_add_code_to_product
 */
class m260104_161919_add_code_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'code', $this->string(50)->null()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'code');
    }
}
