<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Client model
 *
 * @property integer $id
 * @property string $id_type
 * @property string $id_number
 * @property string $full_name
 * @property string $email
 * @property string $whatsapp
 * @property string $phone
 * @property string $address
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Client extends ActiveRecord
{
    const STATUS_PENDING = 1;  // Pendiente
    const STATUS_ACCEPTED = 2;  // Aceptado
    const STATUS_REJECTED = 3;  // Rechazado
    
    const ID_TYPE_FISICO = 'fisico';
    const ID_TYPE_JURIDICO = 'juridico';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'id_type', 'id_number', 'email', 'whatsapp'], 'required'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_REJECTED]],
            [['id_type'], 'string', 'max' => 20],
            [['id_type'], 'in', 'range' => [self::ID_TYPE_FISICO, self::ID_TYPE_JURIDICO]],
            [['id_number'], 'string', 'max' => 50],
            [['full_name'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['whatsapp'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 50],
            [['address'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_type' => 'Tipo de Identificación',
            'id_number' => 'Cédula',
            'full_name' => 'Nombre Completo',
            'email' => 'Correo Electrónico',
            'whatsapp' => 'WhatsApp',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
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
     * Get status label
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_ACCEPTED => 'Aceptado',
            self::STATUS_REJECTED => 'Rechazado',
        ];
        return $statuses[$this->status] ?? 'Desconocido';
    }
    
    /**
     * Get ID type label
     */
    public function getIdTypeLabel()
    {
        $types = [
            self::ID_TYPE_FISICO => 'Físico',
            self::ID_TYPE_JURIDICO => 'Jurídico',
        ];
        return $types[$this->id_type] ?? 'Desconocido';
    }
}
