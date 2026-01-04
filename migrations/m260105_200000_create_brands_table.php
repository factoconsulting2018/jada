<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brand}}`.
 */
class m260105_200000_create_brands_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%brand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-brand-status', '{{%brand}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%brand}}');
    }
}

