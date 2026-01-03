<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%footer_menu_item}}`.
 */
class m260103_170000_create_footer_menu_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%footer_menu_item}}', [
            'id' => $this->primaryKey(),
            'position' => $this->smallInteger()->notNull()->comment('Position 1-4 (column number)'),
            'order' => $this->integer()->notNull()->defaultValue(0)->comment('Order within the column'),
            'page_id' => $this->integer()->null()->comment('Reference to page, if null uses label and url'),
            'label' => $this->string(255)->notNull()->comment('Menu label'),
            'url' => $this->string(500)->null()->comment('URL if not linked to a page'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1: Active, 0: Inactive'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-footer_menu_item-position', '{{%footer_menu_item}}', 'position');
        $this->createIndex('idx-footer_menu_item-order', '{{%footer_menu_item}}', ['position', 'order']);
        $this->createIndex('idx-footer_menu_item-status', '{{%footer_menu_item}}', 'status');
        $this->createIndex('idx-footer_menu_item-page_id', '{{%footer_menu_item}}', 'page_id');

        $this->addForeignKey(
            'fk-footer_menu_item-page_id',
            '{{%footer_menu_item}}',
            'page_id',
            '{{%page}}',
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
        $this->dropForeignKey('fk-footer_menu_item-page_id', '{{%footer_menu_item}}');
        $this->dropTable('{{%footer_menu_item}}');
    }
}

