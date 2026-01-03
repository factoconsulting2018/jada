<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_product}}`.
 */
class m260103_200000_create_quotation_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_product}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'price' => $this->decimal(10, 2)->notNull()->comment('Precio al momento de la cotización'),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-quotation_product-quotation_id', '{{%quotation_product}}', 'quotation_id');
        $this->createIndex('idx-quotation_product-product_id', '{{%quotation_product}}', 'product_id');
        $this->createIndex('idx-quotation_product-unique', '{{%quotation_product}}', ['quotation_id', 'product_id'], true);
        
        $this->addForeignKey(
            'fk-quotation_product-quotation_id',
            '{{%quotation_product}}',
            'quotation_id',
            '{{%quotation}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-quotation_product-product_id',
            '{{%quotation_product}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-quotation_product-product_id', '{{%quotation_product}}');
        $this->dropForeignKey('fk-quotation_product-quotation_id', '{{%quotation_product}}');
        $this->dropTable('{{%quotation_product}}');
    }
}

