<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\Client;

/**
 * Quotation model
 *
 * @property integer $id
 * @property string $id_type
 * @property string $id_number
 * @property string $full_name
 * @property string $email
 * @property string $whatsapp
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property QuotationProduct[] $quotationProducts
 * @property Product[] $products
 */
class Quotation extends ActiveRecord
{
    const STATUS_PENDING = 1;  // Pendiente
    const STATUS_IN_PROCESS = 2;  // En proceso
    const STATUS_DELETED = 3;  // Eliminada
    
    // Mantener compatibilidad con estados antiguos
    const STATUS_NEW = 1;  // Alias para STATUS_PENDING
    const STATUS_PROCESSED = 2;  // Alias para STATUS_IN_PROCESS

    const ID_TYPE_FISICO = 'fisico';
    const ID_TYPE_JURIDICO = 'juridico';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%quotation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_type', 'id_number', 'full_name', 'email', 'whatsapp'], 'required'],
            [['status'], 'integer'],
            [['id_type'], 'string', 'max' => 20],
            [['id_type'], 'in', 'range' => [self::ID_TYPE_FISICO, self::ID_TYPE_JURIDICO]],
            [['id_number'], 'string', 'max' => 50],
            [['full_name'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['whatsapp'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_IN_PROCESS, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_type' => 'Tipo de Identificación',
            'id_number' => 'Cédula',
            'full_name' => 'Nombre Completo',
            'email' => 'Correo Electrónico',
            'whatsapp' => 'WhatsApp',
            'status' => 'Estado',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[QuotationProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationProducts()
    {
        return $this->hasMany(QuotationProduct::class, ['quotation_id' => 'id']);
    }

    /**
     * Gets query for [[Products]] via [[QuotationProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('{{%quotation_product}}', ['quotation_id' => 'id']);
    }

    /**
     * Get total price of all products in quotation
     * @return float
     */
    public function getTotal()
    {
        $total = 0;
        foreach ($this->quotationProducts as $qp) {
            $total += $qp->getSubtotal();
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Sync with Client table
        $this->syncWithClient();
    }
    
    /**
     * Synchronize quotation data with Client table
     */
    public function syncWithClient()
    {
        // Find or create client by id_number and email
        $client = Client::find()
            ->where(['id_number' => $this->id_number])
            ->orWhere(['email' => $this->email])
            ->one();
        
        if (!$client) {
            $client = new Client();
        }
        
        // Update client data from quotation
        $client->id_type = $this->id_type;
        $client->id_number = $this->id_number;
        $client->full_name = $this->full_name;
        $client->email = $this->email;
        $client->whatsapp = $this->whatsapp;
        
        // If client is new, set status to pending
        if ($client->isNewRecord) {
            $client->status = Client::STATUS_PENDING;
        }
        
        $client->save(false);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_IN_PROCESS => 'En proceso',
            self::STATUS_DELETED => 'Eliminada',
            // Compatibilidad con estados antiguos
            self::STATUS_NEW => 'Pendiente',
            self::STATUS_PROCESSED => 'En proceso',
        ];
        return $statuses[$this->status] ?? 'Desconocido';
    }
    
    /**
     * Get count of pending quotations
     * @return int
     */
    public static function getPendingCount()
    {
        return static::find()
            ->where(['status' => self::STATUS_PENDING])
            ->count();
    }

    /**
     * Get ID type label
     */
    public function getIdTypeLabel()
    {
        $types = [
            self::ID_TYPE_FISICO => 'Físico',
            self::ID_TYPE_JURIDICO => 'Jurídico',
        ];
        return $types[$this->id_type] ?? 'Desconocido';
    }

}

