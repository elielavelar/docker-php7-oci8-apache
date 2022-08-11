<?php

namespace backend\models;

use Yii;
use \common\models\Type;
use yii\helpers\StringHelper;
/**
 * This is the model class for table "optionenvironment".
 *
 * @property int $IdOption
 * @property int $IdEnvironmentType
 * @property int $Enabled
 *
 * @property Type $environmentType
 * @property Option $option
 */
class Optionenvironment extends \yii\db\ActiveRecord
{
    const ENABLED_VALUE = 1;
    const DISABLED_VALUE = 0;
    public $setting = [];
    private $envtypes = [];
    private $_envoptions = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'optionenvironment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdOption', 'IdEnvironmentType'], 'required'],
            [['IdOption', 'IdEnvironmentType', 'Enabled'], 'integer'],
            [['IdOption', 'IdEnvironmentType'], 'unique', 'targetAttribute' => ['IdOption', 'IdEnvironmentType']],
            [['IdEnvironmentType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdEnvironmentType' => 'Id']],
            [['IdOption'], 'exist', 'skipOnError' => true, 'targetClass' => Option::class, 'targetAttribute' => ['IdOption' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdOption' => 'Id Option',
            'IdEnvironmentType' => 'Id Environment Type',
            'Enabled' => 'Enabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnvironmentType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdEnvironmentType']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Options::class, ['Id' => 'IdOption']);
    }
    
    public function _setEnvironments(){
        try {
            $this->_getAllEnvironmentOption();
            $this->_setEnvironmentsOptions();
            $this->_iterateEnvironmentOptions();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getAllEnvironmentOption(){
        try {
            $this->envtypes = Type::find()->where(['KeyWord' => StringHelper::basename(self::class)])->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setEnvironmentsOptions(){
        try {
            foreach ($this->setting as $key => $value){
                array_push($this->_envoptions, $value);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateEnvironmentOptions(){
        try {
            foreach ($this->envtypes as $type){
                $env = self::findOne(['IdOption' => $this->IdOption, 'IdEnvironmentType' => $type->Id]);
                if(empty($env)){
                    $env = new Optionenvironment();
                    $env->IdOption = $this->IdOption;
                    $env->IdEnvironmentType = $type->Id;
                }
                $env->Enabled = in_array($type->Id, $this->_envoptions) ? 1:0;
                if(!$env->save()){
                    $message = $this->_getErrors($env->errors);
                    throw new \Exception($message, 92001);  
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _resetAllEnvironments(){
        try {
            $this->_envoptions = [];
            $this->setting = [];
            $this->_getAllEnvironmentOption();
            $this->_iterateEnvironmentOptions();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
     
    
    public function _getErrors($errors = NULL){
        try {
            return StringHelper::basename(self::class).': '.\Yii::$app->customFunctions->getErrors($errors);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
