<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * Banner model
 *
 * @property integer $id
 * @property string $title
 * @property string $subtitle
 * @property string $image
 * @property string $link
 * @property integer $order
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Banner extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%banner}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['subtitle', 'link'], 'string', 'max' => 255],
            [['order', 'status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['order'], 'default', 'value' => 0],
            [['image'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 5 * 1024 * 1024, 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Título',
            'subtitle' => 'Subtítulo',
            'image' => 'Imagen',
            'imageFile' => 'Imagen',
            'link' => 'Enlace',
            'order' => 'Orden',
            'status' => 'Estado',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
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
     * Upload image
     */
    public function upload()
    {
        if ($this->imageFile) {
            $path = Yii::getAlias('@webroot/uploads/banners/');
            FileHelper::createDirectory($path);
            
            $fileName = uniqid() . '_' . time() . '.' . $this->imageFile->extension;
            $filePath = $path . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                // Delete old image
                if ($this->image && file_exists(Yii::getAlias('@webroot') . $this->image)) {
                    unlink(Yii::getAlias('@webroot') . $this->image);
                }
                $this->image = '/uploads/banners/' . $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * Get image URL
     */
    public function getImageUrl()
    {
        if ($this->image) {
            return Yii::getAlias('@web') . $this->image;
        }
        return Yii::getAlias('@web') . '/images/no-image.png';
    }

    /**
     * Get active banners ordered
     */
    public static function getActiveBanners()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['order' => SORT_ASC])
            ->all();
    }
}

