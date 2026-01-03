<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * QuotationProduct model
 *
 * @property integer $id
 * @property integer $quotation_id
 * @property integer $product_id
 * @property integer $quantity
 * @property float $price
 * @property integer $created_at
 *
 * @property Quotation $quotation
 * @property Product $product
 */
class QuotationProduct extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%quotation_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'product_id', 'quantity', 'price'], 'required'],
            [['quotation_id', 'product_id', 'quantity'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
            [['price'], 'number', 'min' => 0],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotation::class, 'targetAttribute' => ['quotation_id' => 'id']],
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
            'quotation_id' => 'Cotización',
            'product_id' => 'Producto',
            'quantity' => 'Cantidad',
            'price' => 'Precio',
            'created_at' => 'Fecha de Creación',
        ];
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
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Quotation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['id' => 'quotation_id']);
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
     * Get subtotal (price * quantity)
     * @return float
     */
    public function getSubtotal()
    {
        return $this->price * $this->quantity;
    }
}

