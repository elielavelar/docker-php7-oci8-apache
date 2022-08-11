<?php

namespace common\models;

use Yii;
use Exception;

/**
 * This is the model class for table "extendedmodelkeyconditions".
 *
 * @property int $Id
 * @property int $c
 * @property string $AttributeName
 * @property string $Value
 * @property string $Description
 *
 * @property Extendedmodelkeysource $extendedModelKeySource
 */
class Extendedmodelkeycondition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelkeyconditions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelKeySource', 'AttributeName'], 'required'],
            [['IdExtendedModelKey'], 'integer'],
            [['Value', 'Description'], 'string'],
            [['AttributeName'], 'string', 'max' => 50],
            [['IdExtendedModelKeySource'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelkeysource::class, 'targetAttribute' => ['IdExtendedModelKeySource' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelKeySource' => 'Modelo',
            'AttributeName' => 'Atributo',
            'Value' => 'Valor',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelKeySource()
    {
        return $this->hasOne(Extendedmodelkeysource::class, ['Id' => 'IdExtendedModelKeySource']);
    }
    
}
