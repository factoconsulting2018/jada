<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * SponsorBanner model
 *
 * @property integer $id
 * @property integer $position
 * @property string $image
 * @property string $link
 * @property string $title
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class SponsorBanner extends ActiveRecord
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
        return '{{%sponsor_banner}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'required'],
            [['position'], 'integer', 'min' => 1, 'max' => 4],
            [['link', 'title'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
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
            'position' => 'Posición',
            'image' => 'Imagen',
            'imageFile' => 'Imagen',
            'link' => 'Enlace',
            'title' => 'Título',
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
            if ($insert) {
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }

    /**
     * Upload image file
     * @return bool
     */
    public function upload()
    {
        if ($this->imageFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@webroot/uploads/sponsors/');
        if (!file_exists($uploadPath)) {
            FileHelper::createDirectory($uploadPath, 0755, true);
        }

        $fileName = uniqid() . '.' . $this->imageFile->extension;
        $filePath = $uploadPath . $fileName;

        if ($this->imageFile->saveAs($filePath)) {
            // Delete old image if exists
            if ($this->image && file_exists(Yii::getAlias('@webroot') . $this->image)) {
                @unlink(Yii::getAlias('@webroot') . $this->image);
            }
            
            $this->image = '/uploads/sponsors/' . $fileName;
            return true;
        }

        return false;
    }

    /**
     * Get image URL
     * @return string
     */
    public function getImageUrl()
    {
        if ($this->image) {
            return Yii::getAlias('@web') . $this->image;
        }
        return '';
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->status === self::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
    }

    /**
     * Get all active sponsor banners ordered by position
     * @return SponsorBanner[]
     */
    public static function getActiveBanners()
    {
        return self::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['!=', 'image', ''])
            ->orderBy(['position' => SORT_ASC])
            ->all();
    }

    /**
     * Get banner by position
     * @param integer $position
     * @return SponsorBanner|null
     */
    public static function getByPosition($position)
    {
        return self::find()
            ->where(['position' => $position])
            ->one();
    }
}


