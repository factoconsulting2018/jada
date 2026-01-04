<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * ProductTechnicalSpec model
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $file_path
 * @property string $name
 * @property integer $order
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Product $product
 */
class ProductTechnicalSpec extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_technical_specs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'file_path'], 'required'],
            [['product_id', 'order'], 'integer'],
            [['file_path', 'name'], 'string', 'max' => 255],
            [['order'], 'default', 'value' => 0],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => 10 * 1024 * 1024, 'checkExtensionByMimeType' => false],
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
            'file_path' => 'Ruta del Archivo',
            'name' => 'Nombre del Documento',
            'order' => 'Orden',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
            'file' => 'Archivo PDF',
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
            $this->updated_at = time();
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Upload file
     */
    public function upload()
    {
        if ($this->file) {
            $path = Yii::getAlias('@webroot/uploads/products/pdfs/');
            FileHelper::createDirectory($path);
            
            $fileName = uniqid() . '_' . time() . '.' . $this->file->extension;
            $filePath = $path . $fileName;
            
            if ($this->file->saveAs($filePath)) {
                // Delete old file if exists
                if ($this->file_path && file_exists(Yii::getAlias('@webroot') . $this->file_path)) {
                    @unlink(Yii::getAlias('@webroot') . $this->file_path);
                }
                
                $this->file_path = '/uploads/products/pdfs/' . $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * Get file URL
     */
    public function getFileUrl()
    {
        if ($this->file_path) {
            return Yii::getAlias('@web') . $this->file_path;
        }
        return null;
    }

    /**
     * Get display name
     */
    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }
        if ($this->file_path) {
            return basename($this->file_path);
        }
        return 'Documento sin nombre';
    }
}

