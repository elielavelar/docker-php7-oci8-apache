<?php

namespace common\models;
use Yii;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\CustomActiveRecord;
/**
 * This is the model class for table "catalogs".
 *
 * @property int $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property int $IdState
 * @property string $Description
 *
 * @property State $state
 * @property Catalogversion[] $catalogversions
 */
class Catalog extends CustomActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code', 'IdState'], 'required'],
            [['IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name', 'KeyWord'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 30],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'KeyWord' => 'Llave',
            'Code' => 'Código',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    public function getStates(){
        return ArrayHelper::map(State::findAll(['KeyWord' => StringHelper::basename(self::class)]), 'Id', 'Name');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogversions()
    {
        return $this->hasMany(Catalogversion::class, ['IdCatalog' => 'Id']);
    }
}
