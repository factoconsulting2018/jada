<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%main_menu_item}}`.
 */
class m260104_131023_create_main_menu_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%main_menu_item}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(20)->notNull()->comment('link, page'),
            'label' => $this->string(255)->notNull(),
            'url' => $this->string(500)->null(),
            'page_id' => $this->integer()->null(),
            'identifier' => $this->string(50)->null()->comment('home, products, quotation, admin'),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1: Active, 0: Inactive'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-main_menu_item-order', '{{%main_menu_item}}', 'order');
        $this->createIndex('idx-main_menu_item-status', '{{%main_menu_item}}', 'status');
        $this->createIndex('idx-main_menu_item-type', '{{%main_menu_item}}', 'type');
        $this->createIndex('idx-main_menu_item-identifier', '{{%main_menu_item}}', 'identifier');

        $this->addForeignKey(
            'fk-main_menu_item-page_id',
            '{{%main_menu_item}}',
            'page_id',
            '{{%page}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Insert default menu items
        $time = time();
        $this->batchInsert('{{%main_menu_item}}', 
            ['type', 'label', 'url', 'identifier', 'order', 'status', 'created_at', 'updated_at'], 
            [
                ['link', 'Inicio', '/', 'home', 0, 1, $time, $time],
                ['link', 'Productos', '/products', 'products', 1, 1, $time, $time],
                ['link', 'Cotización', '/quotation', 'quotation', 3, 1, $time, $time],
                ['link', 'Admin', '/admin/login', 'admin', 4, 1, $time, $time],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-main_menu_item-page_id', '{{%main_menu_item}}');
        $this->dropTable('{{%main_menu_item}}');
    }
}
