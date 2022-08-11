<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "fieldscatalogs".
 *
 * @property int $Id
 * @property int $IdField
 * @property string $Name
 * @property string $Value
 * @property int $Sort
 * @property int $IdState
 * @property string $Description
 *
 * @property Field $field
 * @property State $state
 */
class Fieldcatalog extends \yii\db\ActiveRecord
{
    const SORT_DEFAULT_VALUE = 1;
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fieldcatalog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdField', 'Name', 'IdState'], 'required'],
            [['IdField', 'Sort', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name', 'Value'], 'string', 'max' => 100],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['IdField' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            ['Sort','default','value' => self::SORT_DEFAULT_VALUE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdField' => 'Campo',
            'Name' => 'Nombre',
            'Value' => 'Valor',
            'Sort' => 'Orden',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
        ];
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
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $states = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($states, 'Id', 'Name');
    }
}
