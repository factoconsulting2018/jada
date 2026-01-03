<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property int $status
 * @property int $show_in_menu
 * @property int $menu_order
 * @property int $created_at
 * @property int $updated_at
 */
class Page extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['content'], 'string'],
            [['status', 'show_in_menu', 'menu_order', 'created_at', 'updated_at'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['slug'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'El slug solo puede contener letras minúsculas, números y guiones.'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['show_in_menu'], 'boolean'],
            [['menu_order'], 'integer', 'min' => 0],
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
            'slug' => 'URL (Slug)',
            'content' => 'Contenido',
            'status' => 'Estado',
            'show_in_menu' => 'Mostrar en Menú',
            'menu_order' => 'Orden en Menú',
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
            
            // Generate slug from title if empty
            if (empty($this->slug)) {
                $this->slug = Inflector::slug($this->title);
            }
            
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
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_INACTIVE => 'Inactivo',
        ];
        return $statuses[$this->status] ?? 'Desconocido';
    }

    /**
     * Get URL for the page
     */
    public function getUrl()
    {
        return \yii\helpers\Url::to(['/page/view', 'slug' => $this->slug]);
    }

    /**
     * Find active pages for menu
     */
    public static function getMenuPages()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE, 'show_in_menu' => 1])
            ->orderBy(['menu_order' => SORT_ASC, 'title' => SORT_ASC])
            ->all();
    }

    /**
     * Gets query for [[FooterMenuItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFooterMenuItems()
    {
        return $this->hasMany(\app\models\FooterMenuItem::class, ['page_id' => 'id']);
    }
}

