<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * ParallaxBackground model
 *
 * @property integer $id
 * @property string $section
 * @property string $image
 * @property string $title
 * @property integer $status
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class ParallaxBackground extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%parallax_background}}';
    }

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['section', 'status'], 'required'],
            [['status', 'position'], 'integer'],
            [['section'], 'string', 'max' => 100],
            [['image', 'title'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['position'], 'default', 'value' => 0],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 10 * 1024 * 1024, 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Sección',
            'image' => 'Imagen de Fondo',
            'imageFile' => 'Imagen de Fondo',
            'title' => 'Título',
            'status' => 'Estado',
            'position' => 'Posición',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Get available sections
     * @return array
     */
    public static function getSections()
    {
        return [
            'products' => 'Productos (Index)',
            'categories' => 'Categorías',
            'products_page' => 'Productos (Página)',
        ];
    }

    /**
     * Get section label
     * @return string
     */
    public function getSectionLabel()
    {
        $sections = self::getSections();
        return $sections[$this->section] ?? $this->section;
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->status == self::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
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
     * Upload image
     * @return bool
     */
    public function upload()
    {
        if ($this->imageFile) {
            $uploadPath = Yii::getAlias('@webroot/uploads/parallax/');
            FileHelper::createDirectory($uploadPath, 0775, true);
            
            $fileName = uniqid() . '_' . time() . '.' . $this->imageFile->extension;
            $filePath = $uploadPath . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                // Delete old image if exists
                if ($this->image && file_exists(Yii::getAlias('@webroot') . $this->image)) {
                    @unlink(Yii::getAlias('@webroot') . $this->image);
                }
                $this->image = '/uploads/parallax/' . $fileName;
                return true;
            }
        }
        return true; // Return true even if no file uploaded (to allow saving without new image)
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
     * Get active backgrounds by section
     * @param string $section
     * @return static[]
     */
    public static function getActiveBySection($section)
    {
        return static::find()
            ->where(['section' => $section, 'status' => self::STATUS_ACTIVE])
            ->orderBy(['position' => SORT_ASC])
            ->all();
    }
}

