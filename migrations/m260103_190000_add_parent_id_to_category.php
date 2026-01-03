<?php

use yii\db\Migration;

/**
 * Handles adding parent_id to category table.
 */
class m260103_190000_add_parent_id_to_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category}}', 'parent_id', $this->integer()->null()->after('id'));
        
        // creates index for column `parent_id`
        $this->createIndex(
            'idx-category-parent_id',
            '{{%category}}',
            'parent_id'
        );

        // add foreign key for table `{{%category}}`
        $this->addForeignKey(
            'fk-category-parent_id',
            '{{%category}}',
            'parent_id',
            '{{%category}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%category}}`
        $this->dropForeignKey(
            'fk-category-parent_id',
            '{{%category}}'
        );

        // drops index for column `parent_id`
        $this->dropIndex(
            'idx-category-parent_id',
            '{{%category}}'
        );

        $this->dropColumn('{{%category}}', 'parent_id');
    }
}

