<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_related}}`.
 */
class m260103_140000_create_product_related_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_related}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull()->comment('ID del producto principal'),
            'related_product_id' => $this->integer()->notNull()->comment('ID del producto relacionado'),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-product_related-product_id', '{{%product_related}}', 'product_id');
        $this->createIndex('idx-product_related-related_product_id', '{{%product_related}}', 'related_product_id');
        $this->createIndex('idx-product_related-unique', '{{%product_related}}', ['product_id', 'related_product_id'], true);
        
        $this->addForeignKey(
            'fk-product_related-product_id',
            '{{%product_related}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-product_related-related_product_id',
            '{{%product_related}}',
            'related_product_id',
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
        $this->dropForeignKey('fk-product_related-related_product_id', '{{%product_related}}');
        $this->dropForeignKey('fk-product_related-product_id', '{{%product_related}}');
        $this->dropTable('{{%product_related}}');
    }
}

