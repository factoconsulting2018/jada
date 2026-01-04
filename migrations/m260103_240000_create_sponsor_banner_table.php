<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sponsor_banner}}`.
 */
class m260103_240000_create_sponsor_banner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sponsor_banner}}', [
            'id' => $this->primaryKey(),
            'position' => $this->integer()->notNull()->comment('Position 1-4'),
            'image' => $this->string(255)->notNull(),
            'link' => $this->string(255),
            'title' => $this->string(255),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-sponsor_banner-position', '{{%sponsor_banner}}', 'position');
        $this->createIndex('idx-sponsor_banner-status', '{{%sponsor_banner}}', 'status');
        
        // Insert 4 empty sponsor banners
        for ($i = 1; $i <= 4; $i++) {
            $this->insert('{{%sponsor_banner}}', [
                'position' => $i,
                'image' => '',
                'link' => '',
                'title' => 'Patrocinador ' . $i,
                'status' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sponsor_banner}}');
    }
}


