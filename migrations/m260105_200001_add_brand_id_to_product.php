<?php

use yii\db\Migration;

/**
 * Handles adding brand_id to table `{{%product}}`.
 */
class m260105_200001_add_brand_id_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'brand_id', $this->integer()->null());
        $this->createIndex('idx-product-brand_id', '{{%product}}', 'brand_id');
        $this->addForeignKey(
            'fk-product-brand_id',
            '{{%product}}',
            'brand_id',
            '{{%brand}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product-brand_id', '{{%product}}');
        $this->dropIndex('idx-product-brand_id', '{{%product}}');
        $this->dropColumn('{{%product}}', 'brand_id');
    }
}

