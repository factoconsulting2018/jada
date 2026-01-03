<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation}}`.
 */
class m260103_120000_create_quotation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'id_type' => $this->string(20)->notNull()->comment('fisico o juridico'),
            'id_number' => $this->string(50)->notNull()->comment('Cédula'),
            'full_name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'whatsapp' => $this->string(50)->notNull(),
            'product_image' => $this->string(255)->comment('Ruta de la imagen del producto adjuntada'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1: Nueva, 2: Procesada'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-quotation-product_id', '{{%quotation}}', 'product_id');
        $this->createIndex('idx-quotation-status', '{{%quotation}}', 'status');
        $this->createIndex('idx-quotation-email', '{{%quotation}}', 'email');
        
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-quotation-product_id', '{{%quotation}}');
        $this->dropTable('{{%quotation}}');
    }
}

