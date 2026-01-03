<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * Category model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Product[] $products
 */
class Category extends ActiveRecord
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
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['status', 'parent_id'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 5 * 1024 * 1024, 'checkExtensionByMimeType' => false],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['parent_id'], 'compare', 'compareAttribute' => 'id', 'operator' => '!==', 'message' => 'Una categoría no puede ser su propia padre.'],
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
            'image' => 'Imagen',
            'imageFile' => 'Imagen',
            'status' => 'Estado',
            'parent_id' => 'Categoría Padre',
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
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id'])
            ->where(['status' => Product::STATUS_ACTIVE]);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * Get active categories with no parent (main categories)
     * @return Category[]
     */
    public static function getMainCategories()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE, 'parent_id' => null])
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    /**
     * Get active subcategories for a parent category
     * @param int $parentId
     * @return Category[]
     */
    public static function getSubcategories($parentId)
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE, 'parent_id' => $parentId])
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    /**
     * Upload image
     */
    public function upload()
    {
        if ($this->imageFile) {
            $path = Yii::getAlias('@webroot/uploads/categories/');
            FileHelper::createDirectory($path);
            
            $fileName = uniqid() . '_' . time() . '.' . $this->imageFile->extension;
            $filePath = $path . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                // Delete old image
                if ($this->image && file_exists(Yii::getAlias('@webroot') . $this->image)) {
                    unlink(Yii::getAlias('@webroot') . $this->image);
                }
                $this->image = '/uploads/categories/' . $fileName;
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
}

