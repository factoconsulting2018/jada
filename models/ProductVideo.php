<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ProductVideo model
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $video_url
 * @property string $name
 * @property integer $order
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Product $product
 */
class ProductVideo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_videos}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'video_url'], 'required'],
            [['product_id', 'order'], 'integer'],
            [['video_url'], 'string', 'max' => 500],
            [['video_url'], 'url', 'defaultScheme' => 'https'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'default', 'value' => 0],
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
            'video_url' => 'URL del Video',
            'name' => 'Nombre del Video',
            'order' => 'Orden',
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
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYouTubeVideoId()
    {
        if (!$this->video_url) {
            return null;
        }

        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/',
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
     * Get display name
     */
    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }
        return 'Ver Video';
    }
}

