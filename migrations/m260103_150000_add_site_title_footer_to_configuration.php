<?php

use yii\db\Migration;

/**
 * Handles adding site_title and footer_text to configuration table.
 */
class m260103_150000_add_site_title_footer_to_configuration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert site title configuration
        $this->insert('{{%configuration}}', [
            'key' => 'site_title',
            'value' => 'Tienda Online',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        
        // Insert footer text configuration
        $this->insert('{{%configuration}}', [
            'key' => 'footer_text',
            'value' => '© ' . date('Y') . ' Tienda Online. Todos los derechos reservados.',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%configuration}}', ['key' => 'site_title']);
        $this->delete('{{%configuration}}', ['key' => 'footer_text']);
    }
}

