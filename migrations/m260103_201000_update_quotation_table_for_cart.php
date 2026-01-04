<?php

use yii\db\Migration;

/**
 * Handles updating quotation table for cart system.
 * Removes product_id and product_image as they are now in quotation_product table.
 */
class m260103_201000_update_quotation_table_for_cart extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Drop foreign key if exists
        $this->dropForeignKey('fk-quotation-product_id', '{{%quotation}}');
        
        // Drop indexes
        $this->dropIndex('idx-quotation-product_id', '{{%quotation}}');
        
        // Remove product_id and product_image columns (data will be in quotation_product table)
        $this->dropColumn('{{%quotation}}', 'product_id');
        $this->dropColumn('{{%quotation}}', 'product_image');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Add back product_id column
        $this->addColumn('{{%quotation}}', 'product_id', $this->integer()->null()->after('id'));
        
        // Add back product_image column
        $this->addColumn('{{%quotation}}', 'product_image', $this->string(255)->null()->after('whatsapp'));
        
        // Recreate index
        $this->createIndex('idx-quotation-product_id', '{{%quotation}}', 'product_id');
        
        // Recreate foreign key
        $this->addForeignKey(
            'fk-quotation-product_id',
            '{{%quotation}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}


