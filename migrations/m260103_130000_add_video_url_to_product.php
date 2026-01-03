<?php

use yii\db\Migration;

/**
 * Handles adding video_url column to table `{{%product}}`.
 */
class m260103_130000_add_video_url_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'video_url', $this->string(500)->null()->after('images')->comment('URL del video de YouTube'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'video_url');
    }
}

