<?php

use yii\db\Migration;

/**
 * Handles adding video_url to table `{{%banner}}`.
 */
class m260104_195055_add_video_url_to_banner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%banner}}', 'video_url', $this->string(500)->null()->after('image'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%banner}}', 'video_url');
    }
}
