<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "extendedmodelrecords".
 *
 * @property int $Id
 * @property int $IdExtendedModelKey
 * @property string $AttributeKeyValue
 *
 * @property Extendedmodelfieldvalue[] $extendedmodelfieldvalues
 * @property Extendedmodelkey $extendedModelKey
 */
class Extendedmodelrecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelrecord';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelKey'], 'required'],
            [['IdExtendedModelKey'], 'integer'],
            [['AttributeKeyValue'], 'string'],
            [['IdExtendedModelKey', 'AttributeKeyValue'], 'unique', 'targetAttribute' => ['IdExtendedModelKey', 'AttributeKeyValue']],
            [['IdExtendedModelKey'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelkey::class, 'targetAttribute' => ['IdExtendedModelKey' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelKey' => 'Llave',
            'AttributeKeyValue' => 'Valor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelfieldvalues()
    {
        return $this->hasMany(Extendedmodelfieldvalue::class, ['IdExtendedModelRecord' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelKey()
    {
        return $this->hasOne(Extendedmodelkey::class, ['Id' => 'IdExtendedModelKey']);
    }
}
