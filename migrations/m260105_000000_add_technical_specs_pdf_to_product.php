<?php

use yii\db\Migration;

/**
 * Handles adding technical_specs_pdf to table `{{%product}}`.
 */
class m260105_000000_add_technical_specs_pdf_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'technical_specs_pdf', $this->string(255)->null()->after('video_url'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'technical_specs_pdf');
    }
}

