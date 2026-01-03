<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%footer_menu_item}}".
 *
 * @property int $id
 * @property int $position
 * @property int $order
 * @property int|null $page_id
 * @property string $label
 * @property string|null $url
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Page $page
 */
class FooterMenuItem extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const POSITION_1 = 1;
    const POSITION_2 = 2;
    const POSITION_3 = 3;
    const POSITION_4 = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%footer_menu_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'required'],
            [['position', 'order', 'page_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['position'], 'in', 'range' => [self::POSITION_1, self::POSITION_2, self::POSITION_3, self::POSITION_4]],
            [['label'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 500],
            [['url'], 'url', 'skipOnEmpty' => true],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
            [['label'], 'required', 'when' => function($model) {
                return empty($model->page_id);
            }, 'whenClient' => "function (attribute, value) { return !$('#footermenuitem-page_id').val(); }"],
            [['url'], 'required', 'when' => function($model) {
                return empty($model->page_id);
            }, 'whenClient' => "function (attribute, value) { return !$('#footermenuitem-page_id').val(); }"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'position' => 'Columna',
            'order' => 'Orden',
            'page_id' => 'Página',
            'label' => 'Etiqueta',
            'url' => 'URL',
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
            if ($this->page_id && empty($this->label)) {
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
     * Get position label
     */
    public function getPositionLabel()
    {
        $positions = [
            self::POSITION_1 => 'Columna 1',
            self::POSITION_2 => 'Columna 2',
            self::POSITION_3 => 'Columna 3',
            self::POSITION_4 => 'Columna 4',
        ];
        return $positions[$this->position] ?? 'Desconocido';
    }

    /**
     * Get the URL for this menu item
     */
    public function getMenuUrl()
    {
        if ($this->page_id && $this->page) {
            return \yii\helpers\Url::to(['/page/view', 'slug' => $this->page->slug]);
        }
        return $this->url ?: '#';
    }

    /**
     * Get active menu items grouped by position
     */
    public static function getMenuItemsByPosition()
    {
        $items = static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['position' => SORT_ASC, 'order' => SORT_ASC])
            ->all();

        $grouped = [];
        foreach ($items as $item) {
            if (!isset($grouped[$item->position])) {
                $grouped[$item->position] = [];
            }
            $grouped[$item->position][] = $item;
        }

        return $grouped;
    }
}

