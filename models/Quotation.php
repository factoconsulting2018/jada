<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

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
    const STATUS_NEW = 1;
    const STATUS_PROCESSED = 2;

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
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['status'], 'in', 'range' => [self::STATUS_NEW, self::STATUS_PROCESSED]],
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
     * Get status label
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_NEW => 'Nueva',
            self::STATUS_PROCESSED => 'Procesada',
        ];
        return $statuses[$this->status] ?? 'Desconocido';
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

