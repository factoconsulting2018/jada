<?php

use yii\db\Migration;

/**
 * Class m260104_174401_update_client_table_for_quotations
 */
class m260104_174401_update_client_table_for_quotations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Rename name to full_name
        $this->renameColumn('{{%client}}', 'name', 'full_name');
        
        // Add new columns
        $this->addColumn('{{%client}}', 'id_type', $this->string(20)->after('id'));
        $this->addColumn('{{%client}}', 'id_number', $this->string(50)->after('id_type'));
        $this->addColumn('{{%client}}', 'whatsapp', $this->string(50)->after('email'));
        $this->addColumn('{{%client}}', 'status', $this->smallInteger()->notNull()->defaultValue(1)->after('whatsapp'));
        
        // Rename phone to address (keeping address but making it optional)
        // We'll keep phone as is for now, but add address if it doesn't exist
        if (!$this->getDb()->getTableSchema('{{%client}}')->getColumn('address')) {
            $this->addColumn('{{%client}}', 'address', $this->text()->after('whatsapp'));
        }
        
        // Create index for status
        $this->createIndex('idx-client-status', '{{%client}}', 'status');
        $this->createIndex('idx-client-id-number', '{{%client}}', 'id_number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop indexes
        $this->dropIndex('idx-client-status', '{{%client}}');
        $this->dropIndex('idx-client-id-number', '{{%client}}');
        
        // Drop columns
        $this->dropColumn('{{%client}}', 'status');
        $this->dropColumn('{{%client}}', 'whatsapp');
        $this->dropColumn('{{%client}}', 'id_number');
        $this->dropColumn('{{%client}}', 'id_type');
        
        // Rename full_name back to name
        $this->renameColumn('{{%client}}', 'full_name', 'name');
    }
}
