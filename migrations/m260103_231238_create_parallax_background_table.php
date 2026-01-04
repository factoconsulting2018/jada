<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%parallax_background}}`.
 */
class m260103_231238_create_parallax_background_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%parallax_background}}', [
            'id' => $this->primaryKey(),
            'section' => $this->string(100)->notNull()->comment('Sección donde se aplicará (products, categories, etc)'),
            'image' => $this->string(255)->comment('Ruta de la imagen de fondo'),
            'title' => $this->string(255)->comment('Título opcional para el fondo'),
            'status' => $this->integer()->notNull()->defaultValue(1)->comment('1=Activo, 0=Inactivo'),
            'position' => $this->integer()->defaultValue(0)->comment('Orden de visualización'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `status`
        $this->createIndex(
            'idx-parallax_background-status',
            '{{%parallax_background}}',
            'status'
        );

        // creates index for column `section`
        $this->createIndex(
            'idx-parallax_background-section',
            '{{%parallax_background}}',
            'section'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `status`
        $this->dropIndex(
            'idx-parallax_background-status',
            '{{%parallax_background}}'
        );

        // drops index for column `section`
        $this->dropIndex(
            'idx-parallax_background-section',
            '{{%parallax_background}}'
        );

        $this->dropTable('{{%parallax_background}}');
    }
}
