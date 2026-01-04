<?php

use yii\db\Migration;

/**
 * Handles adding overlay_color and overlay_opacity to table `{{%parallax_background}}`.
 */
class m260104_212601_add_overlay_color_to_parallax_background extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%parallax_background}}', 'overlay_color', $this->string(7)->null()->after('image')->comment('Color hexadecimal para el overlay (ej: #FFFFFF)'));
        $this->addColumn('{{%parallax_background}}', 'overlay_opacity', $this->decimal(3, 2)->defaultValue(0.3)->after('overlay_color')->comment('Opacidad del overlay (0.00 a 1.00)'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%parallax_background}}', 'overlay_opacity');
        $this->dropColumn('{{%parallax_background}}', 'overlay_color');
    }
}
