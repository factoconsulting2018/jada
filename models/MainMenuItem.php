<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%main_menu_item}}".
 *
 * @property int $id
 * @property string $type
 * @property string $label
 * @property string|null $url
 * @property int|null $page_id
 * @property string|null $identifier
 * @property int $order
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Page $page
 */
class MainMenuItem extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const TYPE_LINK = 'link';
    const TYPE_PAGE = 'page';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%main_menu_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'label'], 'required'],
            [['page_id', 'order', 'status', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['type'], 'in', 'range' => [self::TYPE_LINK, self::TYPE_PAGE]],
            [['label'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 500],
            [['identifier'], 'string', 'max' => 50],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
            [['url'], 'required', 'when' => function($model) {
                return $model->type === self::TYPE_LINK;
            }, 'whenClient' => "function (attribute, value) { return $('#mainmenuitem-type').val() === 'link'; }"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Tipo',
            'label' => 'Etiqueta',
            'url' => 'URL',
            'page_id' => 'Página',
            'identifier' => 'Identificador',
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
            
            // Auto-fill label from page if page is selected and label is empty
            if ($this->type === self::TYPE_PAGE && $this->page_id && empty($this->label)) {
                $page = Page::findOne($this->page_id);
                if ($page) {
                    $this->label = $page->title;
                }
            }
            
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
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
     * Get the URL for this menu item
     */
    public function getMenuUrl()
    {
        if ($this->type === self::TYPE_PAGE && $this->page_id && $this->page) {
            return \yii\helpers\Url::to(['/page/view', 'slug' => $this->page->slug]);
        }
        return $this->url ?: '#';
    }

    /**
     * Get active menu items ordered
     */
    public static function getMenuItems()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['order' => SORT_ASC])
            ->all();
    }
}

