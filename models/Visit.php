<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Visit model
 *
 * @property integer $id
 * @property string $ip_address
 * @property string $country
 * @property string $city
 * @property string $region
 * @property float $latitude
 * @property float $longitude
 * @property string $user_agent
 * @property string $page
 * @property string $referrer
 * @property integer $created_at
 */
class Visit extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip_address', 'created_at'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['created_at'], 'integer'],
            [['ip_address'], 'string', 'max' => 45],
            [['country', 'city', 'region'], 'string', 'max' => 100],
            [['user_agent', 'page'], 'string', 'max' => 255],
            [['referrer'], 'string', 'max' => 500],
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
            return true;
        }
        return false;
    }
}

