<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use backend\models\Settingdetail;
use common\models\Extendedmodel;
use Exception;

/**
 * This is the model class for table "registredmodels".
 *
 * @property int $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $NameSpace
 * @property string $CompletePath
 * @property int $EnableExtended
 * @property string $Description
 *
 * @property Extendedmodelkeysource[] $extendedmodelkeysources 
 * @property Extendedmodel $extendedmodel
 * @property Fieldcatalogsource[] $fieldcatalogsources 
 * @property Registredmodelkey[] $registredmodelkeys
 * @property Transactionmodel $transactionmodel
 */
class Registredmodel extends \yii\db\ActiveRecord
{
    const _NAMESPACE_ = 'NameSpace';
    const _NAMESPACE_CODE_ = 'NESP';
    
    const EXTENDEDMODEL_ENABLED = 1;
    const EXTENDEDMODEL_DISABLED = 0;
    public $keys = [];
    private $_isNewRecord = false;
    private $_nameSpace = null;
    
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    public $term = '';
    private $model = null;
    public $criteria = [];
    public $keyAttribute = null;
    public $selectedvalue = null;
    private $modelPath = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'registredmodel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'NameSpace'], 'required'],
            [['EnableExtended'], 'integer'],
            [['Description'], 'string'],
            [['Name','NameSpace', 'KeyWord', 'CompletePath'], 'string', 'max' => 100],
            [['KeyWord'], 'unique', 'targetAttribute' => ['NameSpace', 'KeyWord'], 'message' => 'Ya existe el codigo {value} para este Espacio de Nombre'],
            ['EnableExtended', 'default', 'value' => self::EXTENDEDMODEL_DISABLED],
            ['EnableExtended', 'in', 'range' => [self::EXTENDEDMODEL_DISABLED, self::EXTENDEDMODEL_ENABLED]],
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
            'NameSpace' => 'Espacio de Nombre',
            'CompletePath' => 'Ruta Completa',
            'EnableExtended' => 'Modelo Extendido',
            'Description' => 'Descripción',
            'condition' => 'Condición',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelkeysources() {
        return $this->hasMany(Extendedmodelkeysource::class, ['IdRegistredModel' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodel() {
        return $this->hasOne(Extendedmodel::class, ['IdRegistredModel' => 'Id']);
    }
    
    /** 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getFieldcatalogsources() { 
        return $this->hasMany(Fieldcatalogsource::class, ['IdRegistredModel' => 'Id']); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredmodelkeys() {
        return $this->hasMany(Registredmodelkey::class, ['IdRegistredModel' => 'Id']);
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getTransactionmodel() {
        return $this->hasOne(Transactionmodel::class, ['IdRegistredModel' => 'Id']);
    }
    
    public function afterFind() {
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        $this->_isNewRecord = $this->isNewRecord;
        if($this->isNewRecord && empty($this->CompletePath)){
            $this->CompletePath = $this->NameSpace.'\\models';
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            ($this->_isNewRecord && count($this->keys) > 0) ? $this->_saveKeys() : null;
            ($this->EnableExtended == self::EXTENDEDMODEL_ENABLED && empty($this->extendedmodel)) ? $this->_saveExtendedModel() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    private function _saveKeys(){
        try {
            $this->refresh();
            foreach ($this->keys as $key => $value){
                $detail = new Registredmodelkey();
                $detail->IdRegistredModel = $this->Id;
                $detail->AttributeKeyName = $key;
                if(!$detail->save()){
                    $message = Yii::$app->customFunctions->getErrors($detail->errors);
                    $this->addError('KeyWord', $message);
                    throw new Exception($message, 94000);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveExtendedModel(){
        try {
            $this->refresh();
            if(empty($this->extendedmodel)){
                $this->setNameSpace();
                $model = new Extendedmodel();
                $model->IdRegistredModel = $this->Id;
                $model->IdNameSpace = $this->_nameSpace->Id;
                $model->IdState = State::findOne(['KeyWord' => StringHelper::basename(Extendedmodel::class), 'Code' => Extendedmodel::STATE_ACTIVE])->Id;
                if(!$model->save()){
                    $message = \Yii::$app->customFunctions->getErrors($model->getErrors());
                    $this->addError('EnabledExtended', $message);
                    throw new Exception($message, 94001);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getNameSpaces(){
        $settings = Settingdetail::find()
                ->joinWith('setting b')
                ->where([
                    'b.KeyWord' => self::_NAMESPACE_,
                    'b.Code' => self::_NAMESPACE_CODE_
                ])
                ->orderBy([Settingdetail::tableName().'.Sort' => SORT_ASC])
                ->all();
        return ArrayHelper::map($settings, 'Value', 'Name');
    }
    
    public function setNameSpace(){
        try {
            $this->_nameSpace =  Settingdetail::find()
                    ->joinWith('setting b')
                    ->where([
                        'b.KeyWord' => self::_NAMESPACE_,
                        'b.Code' => self::_NAMESPACE_CODE_,
                        Settingdetail::tableName().'.Value' => $this->NameSpace
                    ])
                    ->one();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getNameSpace(){
        try {
            !($this->_nameSpace) ? $this->setNameSpace() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return $this->_nameSpace;
    }
    
    public function getModels(){
        try {
            if(empty($this->CompletePath)){
                $this->setNameSpace();
                $this->CompletePath = $this->_nameSpace->Value."\models";
                $path = Yii::getAlias('@'.$this->_nameSpace->Value).'/models/*';
            } else {
                $_path = explode("\\", $this->CompletePath);
                $_basePath = $_path[0];
                unset($_path[0]);
                $_complementPath = implode('/', $_path);
                $path = Yii::getAlias('@'.$_basePath).'/'.$_complementPath.'/*';
            }
            $this->term = empty($this->term) ? '': $this->term.'*';
            $files = glob($path.$this->term.'.php');
            $result = [];
            foreach ($files as $i => $file){
                $filename = str_replace(".php", "", $file);
                $basename = StringHelper::basename($filename);
                $result[] = ['id' => $basename, 'text' =>$basename,'path' => $this->CompletePath];
            }
            return ['results' => $result];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributesList(){
        try {
            $attributes = $this->getModelAttributes();
            $result = [];
            foreach ($attributes as $key => $value){
                $result[] = ['id' => $key, 'text' => $value];
            }
            return ['results' => $result];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributesValueList(){
        try {
            $this->_loadModel();
            $this->_prepareCriteria();
            $select = !empty($this->keyAttribute) ? $this->keyAttribute : '*';
            $find = $this->model::find()->select($select)->where($this->criteria);
            $values = $find->asArray()
                    ->all();
            $result = [];
            $_attribute = !empty($this->keyAttribute) ? $this->keyAttribute : $this->model->getPrimaryKey(true);
            $attribute = (gettype($_attribute) == 'array') ? key($_attribute): $_attribute;
            foreach ($values as $key => $value){
                $val = isset($value[$attribute]) ? $value[$attribute] : null;
                $result[] = ['id' => $val, 'text' => $val];
            }
            return ['results' => $result];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _prepareCriteria(){
        try {
            $criteria = [];
            foreach ($this->criteria as $key => $value){
                $k = explode('-', $key);
                $keys = array_splice($k, 2, 2);
                isset($criteria[end($keys)]) ? $criteria[end($keys)]['Value']  = $value : $criteria[end($keys)]['Key']  = $value;
            }
            $this->criteria = [];
            foreach ($criteria as $k => $val){
                $this->criteria = array_merge($this->criteria, [$val['Key'] => $val['Value']]);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributes(){
        try {
            $this->_loadModel();
            $attributes = [];
            foreach ($this->model->attributes as $key => $attr){
                $attributes[$key] = $this->model->getAttributeLabel($key);
            }
            return $attributes;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModel(){
        try {
            !$this->model ? $this->_loadModel() : null;
            return $this->model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _loadModel(){
        try {
            $this->modelPath = $this->CompletePath."\\".$this->KeyWord;
            $this->model = new $this->modelPath;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelPath(){
        return $this->modelPath;
    }
}
