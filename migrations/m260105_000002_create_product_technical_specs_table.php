<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_technical_specs}}`.
 */
class m260105_000002_create_product_technical_specs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_technical_specs}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'file_path' => $this->string(255)->notNull(),
            'name' => $this->string(255)->null(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-product_technical_specs-product_id', '{{%product_technical_specs}}', 'product_id');
        
        $this->addForeignKey(
            'fk-product_technical_specs-product_id',
            '{{%product_technical_specs}}',
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
        $this->dropForeignKey('fk-product_technical_specs-product_id', '{{%product_technical_specs}}');
        $this->dropTable('{{%product_technical_specs}}');
    }
}

