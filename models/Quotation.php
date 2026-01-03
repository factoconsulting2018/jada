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
 * @property integer $product_id
 * @property string $id_type
 * @property string $id_number
 * @property string $full_name
 * @property string $email
 * @property string $whatsapp
 * @property string $product_image
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Product $product
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
            [['product_id', 'id_type', 'id_number', 'full_name', 'email', 'whatsapp'], 'required'],
            [['product_id', 'status'], 'integer'],
            [['id_type'], 'string', 'max' => 20],
            [['id_type'], 'in', 'range' => [self::ID_TYPE_FISICO, self::ID_TYPE_JURIDICO]],
            [['id_number'], 'string', 'max' => 50],
            [['full_name'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['whatsapp'], 'string', 'max' => 50],
            [['product_image'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['status'], 'in', 'range' => [self::STATUS_NEW, self::STATUS_PROCESSED]],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Producto',
            'id_type' => 'Tipo de Identificación',
            'id_number' => 'Cédula',
            'full_name' => 'Nombre Completo',
            'email' => 'Correo Electrónico',
            'whatsapp' => 'WhatsApp',
            'product_image' => 'Imagen del Producto',
            'status' => 'Estado',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
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

    /**
     * Get product image URL
     */
    public function getProductImageUrl()
    {
        if ($this->product_image) {
            return Yii::getAlias('@web') . $this->product_image;
        }
        return null;
    }
}

