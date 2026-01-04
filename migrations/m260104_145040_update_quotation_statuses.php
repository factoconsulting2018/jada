<?php

use yii\db\Migration;

/**
 * Class m260104_145040_update_quotation_statuses
 */
class m260104_145040_update_quotation_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Los estados antiguos STATUS_NEW (1) y STATUS_PROCESSED (2) se mantienen
        // pero ahora representan STATUS_PENDING (1) y STATUS_IN_PROCESS (2)
        // Agregamos STATUS_DELETED (3)
        // No necesitamos cambiar datos existentes ya que los valores son compatibles
        // Solo actualizamos la lógica en el modelo
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // No hay cambios en la base de datos que revertir
    }
}
