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
 * @property string $technical_specs_pdf
 * @property string $technical_specs_pdf_name
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
     * @var UploadedFile
     */
    public $technicalSpecsPdfFile;

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
            [['technical_specs_pdf', 'technical_specs_pdf_name', 'qr_label_top', 'qr_label_bottom'], 'string', 'max' => 255],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 20, 'checkExtensionByMimeType' => false],
            [['technicalSpecsPdfFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => 10 * 1024 * 1024, 'checkExtensionByMimeType' => false],
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
            'technical_specs_pdf' => 'Especificaciones Técnicas (PDF)',
            'technical_specs_pdf_name' => 'Nombre del Documento',
            'technicalSpecsPdfFile' => 'Especificaciones Técnicas (PDF)',
            'qr_label_top' => 'Etiqueta Superior QR',
            'qr_label_bottom' => 'Etiqueta Inferior QR',
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
     * Get technical specs
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicalSpecs()
    {
        return $this->hasMany(ProductTechnicalSpec::class, ['product_id' => 'id'])
            ->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    /**
     * Get videos
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(ProductVideo::class, ['product_id' => 'id'])
            ->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC]);
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

    /**
     * Upload technical specs PDF
     */
    public function uploadTechnicalSpecsPdf()
    {
        if ($this->technicalSpecsPdfFile) {
            $path = Yii::getAlias('@webroot/uploads/products/pdfs/');
            FileHelper::createDirectory($path);
            
            $fileName = uniqid() . '_' . time() . '.' . $this->technicalSpecsPdfFile->extension;
            $filePath = $path . $fileName;
            
            if ($this->technicalSpecsPdfFile->saveAs($filePath)) {
                // Delete old PDF if exists
                if ($this->technical_specs_pdf && file_exists(Yii::getAlias('@webroot') . $this->technical_specs_pdf)) {
                    @unlink(Yii::getAlias('@webroot') . $this->technical_specs_pdf);
                }
                
                $this->technical_specs_pdf = '/uploads/products/pdfs/' . $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * Get technical specs PDF URL
     */
    public function getTechnicalSpecsPdfUrl()
    {
        if ($this->technical_specs_pdf) {
            return Yii::getAlias('@web') . $this->technical_specs_pdf;
        }
        return null;
    }

    /**
     * Get technical specs PDF filename
     */
    public function getTechnicalSpecsPdfFilename()
    {
        // Return custom name if set, otherwise return the filename
        if ($this->technical_specs_pdf_name) {
            return $this->technical_specs_pdf_name;
        }
        if ($this->technical_specs_pdf) {
            return basename($this->technical_specs_pdf);
        }
        return null;
    }
}

