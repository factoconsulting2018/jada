<?php

use yii\db\Migration;

/**
 * Handles adding qr_label_top and qr_label_bottom to table `{{%product}}`.
 */
class m260105_000004_add_qr_fields_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'qr_label_top', $this->string(255)->null()->after('technical_specs_pdf_name'));
        $this->addColumn('{{%product}}', 'qr_label_bottom', $this->string(255)->null()->after('qr_label_top'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'qr_label_bottom');
        $this->dropColumn('{{%product}}', 'qr_label_top');
    }
}

