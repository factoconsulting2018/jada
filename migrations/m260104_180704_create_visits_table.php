<?php

use yii\db\Migration;

/**
 * Class m260104_180704_create_visits_table
 */
class m260104_180704_create_visits_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%visit}}', [
            'id' => $this->primaryKey(),
            'ip_address' => $this->string(45)->notNull(),
            'country' => $this->string(100),
            'city' => $this->string(100),
            'region' => $this->string(100),
            'latitude' => $this->decimal(10, 8),
            'longitude' => $this->decimal(11, 8),
            'user_agent' => $this->string(255),
            'page' => $this->string(255),
            'referrer' => $this->string(500),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-visit-created_at', '{{%visit}}', 'created_at');
        $this->createIndex('idx-visit-country', '{{%visit}}', 'country');
        $this->createIndex('idx-visit-page', '{{%visit}}', 'page');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%visit}}');
    }
}
