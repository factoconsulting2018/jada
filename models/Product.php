<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\Json;

/**
 * Product model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property integer $category_id
 * @property string $image
 * @property string $images
 * @property string $video_url
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Category $category
 */
class Product extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var UploadedFile[]
     */
    public $imageFiles;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'category_id'], 'required'],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
            [['category_id', 'status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 255],
            [['images'], 'string'],
            [['video_url'], 'string', 'max' => 500],
            [['video_url'], 'url', 'defaultScheme' => 'https'],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 20, 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'price' => 'Precio',
            'category_id' => 'Categoría',
            'image' => 'Imagen Principal',
            'images' => 'Imágenes Adicionales',
            'imageFiles' => 'Imágenes',
            'video_url' => 'URL de Video (YouTube)',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Get related products
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'related_product_id'])
            ->viaTable('{{%product_related}}', ['product_id' => 'id'])
            ->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get all related product IDs
     */
    public function getRelatedProductIds()
    {
        return (new \yii\db\Query())
            ->select('related_product_id')
            ->from('{{%product_related}}')
            ->where(['product_id' => $this->id])
            ->column();
    }

    /**
     * Upload images
     */
    public function upload()
    {
        if ($this->imageFiles) {
            $path = Yii::getAlias('@webroot/uploads/products/');
            FileHelper::createDirectory($path);
            
            $uploadedImages = [];
            $isFirst = true;

            foreach ($this->imageFiles as $file) {
                $fileName = uniqid() . '_' . time() . '.' . $file->extension;
                $filePath = $path . $fileName;
                
                if ($file->saveAs($filePath)) {
                    $imagePath = '/uploads/products/' . $fileName;
                    $uploadedImages[] = $imagePath;
                    
                    // Set first image as main image
                    if ($isFirst && !$this->image) {
                        $this->image = $imagePath;
                        $isFirst = false;
                    }
                }
            }

            // Merge with existing images
            $existingImages = $this->getImagesArray();
            $allImages = array_merge($existingImages, $uploadedImages);
            $this->images = Json::encode($allImages);
            
            return true;
        }
        return false;
    }

    /**
     * Get images array
     */
    public function getImagesArray()
    {
        if ($this->images) {
            return Json::decode($this->images);
        }
        return [];
    }

    /**
     * Get all product images (main + additional)
     */
    public function getAllImages()
    {
        $images = $this->getImagesArray();
        if ($this->image && !in_array($this->image, $images)) {
            array_unshift($images, $this->image);
        }
        return $images;
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
     * Get formatted price
     */
    public function getFormattedPrice()
    {
        return '₡' . number_format($this->price, 2, '.', ',');
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYouTubeVideoId()
    {
        if (!$this->video_url) {
            return null;
        }

        // Extract video ID from various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYouTubeEmbedUrl()
    {
        $videoId = $this->getYouTubeVideoId();
        if ($videoId) {
            return 'https://www.youtube.com/embed/' . $videoId;
        }
        return null;
    }
}

