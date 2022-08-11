<?php

namespace backend\models;

use Yii;
use common\models\CustomActiveRecord;
use common\models\State;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use common\models\Field;
use Exception;

/**
 * This is the model class for table "settingsdetail".
 *
 * @property integer $Id
 * @property integer $IdSetting
 * @property string $Name
 * @property string $Code
 * @property integer $IdType
 * @property integer $IdState
 * @property string $Value
 * @property integer $Sort
 * @property string $Description
 *
 * @property State $state
 * @property Type $type
 * @property Setting $setting
 */
class Settingdetail extends CustomActiveRecord
{
    
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settingdetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdSetting', 'Name', 'Code', 'IdType', 'IdState', 'Value'], 'required','message'=>'{attribute} no puede quedar vacío'],
            [['IdSetting', 'IdType', 'IdState', 'Sort'], 'integer'],
            [['Value', 'Description'], 'string'],
            [['Name'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 50],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['IdSetting'], 'exist', 'skipOnError' => true, 'targetClass' => Setting::class, 'targetAttribute' => ['IdSetting' => 'Id']],
            [['Code'], 'unique', 'targetAttribute' => ['IdSetting', 'Code'], 'message' => 'Ya existe el codigo {value} para este parámetro'],
            [['Sort'], 'unique', 'targetAttribute' => ['IdSetting', 'Sort'], 'message' => 'Ya existe el orden {value} para este parámetro'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdSetting' => 'Id Setting',
            'Name' => 'Nombre',
            'Code' => 'Código',
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'Value' => 'Valor',
            'Sort' => 'Orden',
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
        try {
            return ArrayHelper::map(State::findAll(['KeyWord'=> StringHelper::basename(Setting::class)]), 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }

    public function getTypes(){
        try {
            return ArrayHelper::map(Type::findAll(['KeyWord'=> StringHelper::basename(Field::class)]), 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetting()
    {
        return $this->hasOne(Setting::class, ['Id' => 'IdSetting']);
    }

    public function beforeSave($insert) {
        try {
            if(empty($this->Sort)){
                $this->_getNextSort();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    private function _getNextSort(){
        try {
            $values = self::find()
                    ->where(['IdSetting'=> $this->IdSetting])
                    ->max('Sort');
            $this->Sort = (int)$values + 1;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
