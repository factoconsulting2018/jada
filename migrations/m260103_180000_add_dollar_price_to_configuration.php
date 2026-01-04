<?php

use yii\db\Migration;

/**
 * Handles adding dollar_price and show_dollar_price to configuration table.
 */
class m260103_180000_add_dollar_price_to_configuration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%configuration}}', [
            'key' => 'dollar_price',
            'value' => '500.00',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%configuration}}', [
            'key' => 'show_dollar_price',
            'value' => '0',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%configuration}}', ['key' => 'dollar_price']);
        $this->delete('{{%configuration}}', ['key' => 'show_dollar_price']);
    }
}


