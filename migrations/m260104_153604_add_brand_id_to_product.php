<?php

use yii\db\Migration;

class m260104_153604_add_brand_id_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260104_153604_add_brand_id_to_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260104_153604_add_brand_id_to_product cannot be reverted.\n";

        return false;
    }
    */
}
