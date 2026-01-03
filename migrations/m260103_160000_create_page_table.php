<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page}}`.
 */
class m260103_160000_create_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1: Active, 0: Inactive'),
            'show_in_menu' => $this->boolean()->notNull()->defaultValue(0)->comment('Show in main menu'),
            'menu_order' => $this->integer()->notNull()->defaultValue(0)->comment('Order in menu'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-page-slug', '{{%page}}', 'slug');
        $this->createIndex('idx-page-status', '{{%page}}', 'status');
        $this->createIndex('idx-page-show_in_menu', '{{%page}}', 'show_in_menu');
        $this->createIndex('idx-page-menu_order', '{{%page}}', 'menu_order');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%page}}');
    }
}

