<?php

use yii\db\Migration;

/**
 * Handles adding technical_specs_pdf_name to table `{{%product}}`.
 */
class m260105_000001_add_technical_specs_pdf_name_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'technical_specs_pdf_name', $this->string(255)->null()->after('technical_specs_pdf'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'technical_specs_pdf_name');
    }
}

