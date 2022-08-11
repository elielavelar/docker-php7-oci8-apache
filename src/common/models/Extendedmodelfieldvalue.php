<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "extendedmodelfieldvalue".
 *
 * @property int $Id
 * @property int $IdExtendedModelRecord
 * @property int $IdExtendedModelField
 * @property string $Value
 * @property int $IdFieldCatalog
 * @property int $CustomValue
 * @property string $Description
 *
 * @property Fieldscatalog $fieldCatalog
 * @property Extendedmodelrecord $extendedModelRecord
 * @property Extendedmodelfield $extendedModelField
 */
class Extendedmodelfieldvalue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelfieldvalue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelRecord', 'IdExtendedModelField'], 'required'],
            [['IdExtendedModelRecord', 'IdExtendedModelField', 'IdFieldCatalog', 'CustomValue'], 'integer'],
            [['Value', 'Description'], 'string'],
            [['IdFieldCatalog'], 'exist', 'skipOnError' => true, 'targetClass' => Fieldscatalog::class, 'targetAttribute' => ['IdFieldCatalog' => 'Id']],
            [['IdExtendedModelRecord'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelrecord::class, 'targetAttribute' => ['IdExtendedModelRecord' => 'Id']],
            [['IdExtendedModelField'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelfield::class, 'targetAttribute' => ['IdExtendedModelField' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelRecord' => 'Registro',
            'IdExtendedModelField' => 'Campo',
            'Value' => 'Valor',
            'IdFieldCatalog' => 'Valor Catálogo',
            'CustomValue' => 'Valor Personalizado',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldCatalog()
    {
        return $this->hasOne(Fieldscatalog::class, ['Id' => 'IdFieldCatalog']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelRecord()
    {
        return $this->hasOne(Extendedmodelrecord::class, ['Id' => 'IdExtendedModelRecord']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelField()
    {
        return $this->hasOne(Extendedmodelfield::class, ['Id' => 'IdExtendedModelField']);
    }
}
