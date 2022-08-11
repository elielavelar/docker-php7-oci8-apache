<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "fieldcatalogconditions".
 *
 * @property int $Id
 * @property int $IdFieldCatalogSource
 * @property string $AttributeName
 * @property string $Value
 * @property string $Description
 *
 * @property Fieldcatalogsource $fieldCatalogSource
 */
class Fieldcatalogcondition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fieldcatalogcondition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdFieldCatalogSource', 'AttributeName'], 'required'],
            [['IdFieldCatalogSource'], 'integer'],
            [['Value', 'Description'], 'string'],
            [['AttributeName'], 'string', 'max' => 100],
            [['IdFieldCatalogSource'], 'exist', 'skipOnError' => true, 'targetClass' => Fieldcatalogsource::class, 'targetAttribute' => ['IdFieldCatalogSource' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdFieldCatalogSource' => 'Modelo',
            'AttributeName' => 'Atributo',
            'Value' => 'Valor',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Fieldcatalogsource::class, ['Id' => 'IdFieldCatalogSource']);
    }
}
