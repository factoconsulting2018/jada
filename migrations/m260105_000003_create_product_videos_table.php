<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_videos}}`.
 */
class m260105_000003_create_product_videos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_videos}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'video_url' => $this->string(500)->notNull(),
            'name' => $this->string(255)->null(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-product_videos-product_id', '{{%product_videos}}', 'product_id');
        
        $this->addForeignKey(
            'fk-product_videos-product_id',
            '{{%product_videos}}',
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
        $this->dropForeignKey('fk-product_videos-product_id', '{{%product_videos}}');
        $this->dropTable('{{%product_videos}}');
    }
}

