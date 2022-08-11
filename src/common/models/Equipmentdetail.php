<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equipmentdetail".
 *
 * @property int $Id
 * @property int $IdEquipment
 * @property int $IdField
 * @property string $Value
 * @property int $IdFieldCatalogValue
 * @property int $CustomValue
 * @property string $Description
 *
 * @property Equipment $equipment
 * @property Field $field
 * @property Fieldcatalog $fieldCatalogValue
 */
class Equipmentdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipmentdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdEquipment', 'IdField'], 'required'],
            [['IdEquipment', 'IdField', 'IdFieldCatalogValue', 'CustomValue'], 'integer'],
            [['Value', 'Description'], 'string'],
            [['IdEquipment'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::class, 'targetAttribute' => ['IdEquipment' => 'Id']],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['IdField' => 'Id']],
            [['IdFieldCatalogValue'], 'exist', 'skipOnError' => true, 'targetClass' => Fieldcatalog::class, 'targetAttribute' => ['IdFieldCatalogValue' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdEquipment' => 'Id Equipment',
            'IdField' => 'Id Field',
            'Value' => 'Value',
            'IdFieldCatalogValue' => 'Id Field Catalog Value',
            'CustomValue' => 'Custom Value',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['Id' => 'IdEquipment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::class, ['Id' => 'IdField']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldCatalogValue()
    {
        return $this->hasOne(Fieldcatalog::class, ['Id' => 'IdFieldCatalogValue']);
    }
}
