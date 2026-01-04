<?php

use yii\db\Migration;
use app\models\Quotation;
use app\models\Client;

/**
 * Class m260104_174422_sync_quotations_to_clients
 */
class m260104_174422_sync_quotations_to_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Get all quotations
        $quotations = Quotation::find()->all();
        
        foreach ($quotations as $quotation) {
            // Find or create client by id_number or email
            $client = Client::find()
                ->where(['id_number' => $quotation->id_number])
                ->orWhere(['email' => $quotation->email])
                ->one();
            
            if (!$client) {
                $client = new Client();
            }
            
            // Update client data from quotation
            $client->id_type = $quotation->id_type;
            $client->id_number = $quotation->id_number;
            $client->full_name = $quotation->full_name;
            $client->email = $quotation->email;
            $client->whatsapp = $quotation->whatsapp;
            
            // If client is new, set status to pending
            if ($client->isNewRecord) {
                $client->status = Client::STATUS_PENDING;
            }
            
            $client->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // This migration only syncs data, no schema changes to revert
        echo "m260104_174422_sync_quotations_to_clients cannot be reverted.\n";
        return false;
    }
}

