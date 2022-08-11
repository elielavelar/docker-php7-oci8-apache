<?php

namespace common\models;

use Yii;
use common\models\Registredmodel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use Exception;

/**
 * This is the model class for table "extendedmodelkey".
 *
 * @property int $Id
 * @property int $IdExtendedModel
 * @property string $AttributeKeyName
 * @property string $AttributeKeyValue
 * @property int $EnabledModelSource
 * @property string $AttributeSourceName
 * @property int $IdState
 * @property string $Description
 *
 * @property Extendedmodelfieldgroup[] $extendedmodelfieldgroups
 * @property Extendedmodel $extendedModel
 * @property State $state
 * @property Extendedmodelkeysource[] $extendedmodelkeysources
 * @property Extendedmodelrecord[] $extendedmodelrecords
 */
class Extendedmodelkey extends \yii\db\ActiveRecord
{
    const MODEL_SOURCE_ENABLED = 1;
    const MODEL_SOURCE_DISABLED = 0;
    
    public $value = null;
    private $rmodel = null;
    private $model  = null;
    public $criteria = [];
    public $select = [];
    public $keyAttribute = null;
    public $modelAttributes = [];
    private $condition = [];
    public $conditionform = [];
    public $idRegistredModel = null;
    public $lastId = null;
    private $_isNewRecord = false;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelkey';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModel', 'AttributeKeyName', 'IdState'], 'required'],
            [['IdExtendedModel', 'EnabledModelSource', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['AttributeKeyName', 'AttributeKeyValue','AttributeSourceName'], 'string', 'max' => 100],
            [['AttributeKeyName'], 'unique'
                , 'targetAttribute' => ['IdExtendedModel', 'AttributeKeyName']
                , 'when' => function($model){
                    $search = Extendedmodelkey::find()
                            ->where([
                                'IdExtendedModel' => $model->IdExtendedModel,
                                'AttributeKeyName' => $model->AttributeKeyName,
                                'AttributeKeyValue' => empty($model->AttributeKeyValue) ? null : $model->AttributeKeyValue,
                            ]);
                    !empty($model->Id) ? $search->andWhere('Id != :id', [':id'=> $model->Id]) : null;
                    $count = $search->count();
                    return $count > 0;
                },
            ],
            [['IdExtendedModel'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodel::class, 'targetAttribute' => ['IdExtendedModel' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            ['EnabledModelSource','default', 'value' => self::MODEL_SOURCE_DISABLED],
            ['EnabledModelSource','in', 'range' => [self::MODEL_SOURCE_DISABLED, self::MODEL_SOURCE_ENABLED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModel' => 'Modelo',
            'AttributeKeyName' => 'Nombre Atributo Clave',
            'AttributeKeyValue' => 'Valor',
            'EnabledModelSource' => 'Habilitar Modelo Relacionado',
            'AttributeSourceName' => 'Atributo Relacionado',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
            'value' => 'Valor',
            'condition' => 'Condición',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelfieldgroups()
    {
        return $this->hasMany(Extendedmodelfieldgroup::class, ['IdExtendedModelKey' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModel()
    {
        return $this->hasOne(Extendedmodel::class, ['Id' => 'IdExtendedModel']);
    }
        
    public function getModels() 
    { 
        $models = Registredmodel::find()->all();
        return ArrayHelper::map($models, 'Id', 'Name');
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
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelkeysources()
    {
        return $this->hasMany(Extendedmodelkeysource::class, ['IdExtendedModelKey' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelrecords()
    {
        return $this->hasMany(Extendedmodelrecord::class, ['IdExtendedModelKey' => 'Id']);
    }
    
    public function setCondition($conditions = []){
        $this->condition = $conditions;
    }
    
    public function afterFind() {
        try {
            $this->value = $this->EnabledModelSource ? $this->AttributeKeyValue : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        $this->_isNewRecord = $this->isNewRecord;
        $this->AttributeKeyValue = $this->EnabledModelSource ? $this->value : $this->AttributeKeyValue;
        $this->AttributeKeyValue = empty($this->AttributeKeyValue) ? null: $this->AttributeKeyValue;
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_createDefaultGroup();
            !empty($this->condition) ? $this->_saveConditions() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function loadModelAttributesForm(){
        try {
            $response = [];
            if($this->EnabledModelSource){
                $i = 0;
                $this->idRegistredModel = $this->IdRegistredModelSource;
                $this->_loadModel();
                foreach ($this->extendedmodelkeyconditions as $cond){
                    $this->lastId = $this->tableName().'-condition-'.$i;
                    $response[] = $this->_getModelAttributeForm($cond->AttributeName, $cond->Value);
                    $i++;
                }
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributes(){
        $this->idRegistredModel = $this->IdRegistredModelSource;
        !$this->rmodel ? $this->_loadModel() : null;
        $this->modelAttributes = $this->rmodel ? $this->rmodel->getModelAttributes() : [];
    }
    
    public function getLoadedModelTableName(){
        try {
            !$this->rmodel ? $this->_loadModel() : null;
            !$this->rmodel ? $this->rmodel->tableName() :null;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributesForm($selectValue = null, $inputValue = null){
        try {
            $this->_loadModel();
            return $this->_getModelAttributeForm($selectValue, $inputValue);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getModelAttributeForm($selectValue = null, $inputValue = null){
        try {
            $response = [];
            $attributes = $this->rmodel->getModelAttributes();
            $class = StringHelper::basename(self::class);
            $partsId = $this->lastId ? explode('-', $this->lastId) : [];
            $c = !empty($partsId) ? ( (int) end($partsId) + 1):1;
            $selectId = $this->tableName().'-condition-select-'.$c;
            $labelSelect = Html::label('Llave',$selectId);
            $inputSelect = Html::dropDownList($class."[condition][$c][key]", $selectValue, $attributes, ['class' => 'form-control','id' => $selectId]);
            $divSelect = Html::tag('div', $labelSelect.$inputSelect, ['class' => 'col-6 condition']);
            $response[] = $divSelect;
            $inputId = $this->tableName().'-condition-input-'.$c;
            $label = Html::label('Valor',$inputId);
            $input = Html::textInput($class."[condition][$c][value]", $inputValue, ['class' => 'form-control','id' => $inputId]);
            $div = Html::tag('div', $label.$input, ['class' => 'col-5 condition']);
            $response[] = $div;
            $labelButton = Html::label('&nbsp;','btn-'.$c);
            $button = Html::a('<i class="fas fa-times fa-1x"></i>', "javascript:removeCond($c);",['class' => 'btn btn-remove-cond']);
            $divButton = Html::tag('div', $labelButton.$button, ['class' => 'col-1 condition','id' => 'btn-'.$c]);
            $response[] = $divButton;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _loadModel(){
        try {
            $this->rmodel = Registredmodel::findOne(['Id' => $this->idRegistredModel]);
            $this->model = $this->rmodel->getModel();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveConditions(){
        try {
            $this->refresh();
            $conditions = [];
            $usedcondition = [];
            foreach ($this->extendedmodelkeyconditions as $cond){
                $conditions[$cond->AttributeName] = $cond;
            }
            foreach ($this->condition as $key => $value){
                $criteria = [
                    'IdExtendedModelKey' => $this->Id,
                    'AttributeName' => $value['key'],
                ];
                $cond = isset($conditions[$value['key']]) ? $conditions[$value['key']]:null;
                if(empty($cond)){
                    $cond = new Extendedmodelkeycondition();
                    $cond->attributes = $criteria;
                } 
                $cond->Value = $value['value'];
                if(!$cond->save()){
                    $message = Yii::$app->customFunctions->getErrors($cond->errors);
                    $this->addError('AttributeKeyName', $message);
                    throw new Exception($message, 94000);
                } else {
                    $cond->refresh();
                    array_push($usedcondition, $cond->Id);
                }
            }
            !empty($usedcondition) ? Extendedmodelkeycondition::deleteAll(['AND','IdExtendedModelKey = :idextendedmodel',['NOT IN', 'Id', $usedcondition]],[':idextendedmodel' => $this->Id]) : null;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _createDefaultGroup(){
        try {
            $this->refresh();
            if(Extendedmodelfieldgroup::find()->where(['IdExtendedModelKey' => $this->Id])->count() == 0){
                $model = new Extendedmodelfieldgroup();
                $model->IdExtendedModelKey = $this->Id;
                $model->Name = $model::DEFAULT_GROUP_NAME;
                $model->IdState = State::findOne(['KeyWord' => StringHelper::basename(Extendedmodelfieldgroup::class),'Code' => $model::STATE_ACTIVE])->Id;
                $model->Sort = $model::SORT_DEFAULT_VALUE;
                $model->save();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
