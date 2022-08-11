<?php

namespace common\models;

use Yii;
use common\models\Field;
use common\models\Fieldcatalogcondition;
use common\models\Registredmodel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use Exception;

/**
 * This is the model class for table "fieldcatalogsource".
 *
 * @property int $Id
 * @property int $IdField
 * @property int $IdRegistredModel
 * @property string $TableAlias
 * @property string $RelationString Default value is the Table Key Attribute Name
 * @property int $CustomRelationString
 * @property string $ReturnAttributeNameId
 * @property string $ReturnAttributeNameText
 * @property int $Sort
 * @property int $IdJoinType
 * @property string $Description
 *
 * @property Fieldcatalogcondition[] $fieldcatalogconditions
 * @property Field $field
 * @property Type $joinType
 * @property Registredmodel $registredModel
 */
class Fieldcatalogsource extends \yii\db\ActiveRecord
{
    const JOIN_TYPE_KEYWORD = 'SQLJoinType';
    public $lastId = null;
    private $rmodel = null;
    private $model  = null;
    private $condition = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fieldcatalogsource';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdField', 'IdRegistredModel', 'ReturnAttributeNameText', 'Sort', 'IdJoinType'], 'required'],
            [['IdField', 'IdRegistredModel', 'CustomRelationString', 'Sort', 'IdJoinType'], 'integer'],
            [['Description'], 'string'],
            [['TableAlias'], 'string', 'max' => 50],
            [['RelationString'], 'string', 'max' => 100],
            [['ReturnAttributeNameId', 'ReturnAttributeNameText'], 'string', 'max' => 25],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['IdField' => 'Id']],
            [['IdJoinType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdJoinType' => 'Id']],
            [['IdRegistredModel'], 'exist', 'skipOnError' => true, 'targetClass' => Registredmodel::class, 'targetAttribute' => ['IdRegistredModel' => 'Id']],
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
            'IdRegistredModel' => 'Modelo',
            'TableAlias' => 'Alias de Tabla',
            'RelationString' => 'Cadena de Relación',
            'CustomRelationString' => 'Relación Personalizada',
            'ReturnAttributeKeyId' => 'Atributo Id Retornado',
            'ReturnAttributeKeyText' => 'Atributo Texto Retornado',
            'Sort' => 'Orden',
            'IdJoinType' => 'Tipo de Unión',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldcatalogconditions()
    {
        return $this->hasMany(Fieldcatalogcondition::class, ['IdFieldCatalogSource' => 'Id']);
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
    public function getJoinType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdJoinType']);
    }
    
    public function getJointTypeList(){
        $type = Type::find()
                ->joinWith('state b')
                ->where([
                    'b.Code' => Type::STATUS_ACTIVE,
                    Type::tableName().'.KeyWord' => self::JOIN_TYPE_KEYWORD,
                ])
                ->all();
        return ArrayHelper::map($type, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredModel()
    {
        return $this->hasOne(Registredmodel::class, ['Id' => 'IdRegistredModel']);
    }
    
    public function setCondition($condition = []){
        $this->condition = $condition;
    }
    
    public function saveConditions(){
        try {
            $this->_saveConditions();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveConditions(){
        try {
            $this->refresh();
            $conditions = [];
            $usedcondition = [];
            if(!empty($this->condition)){
                foreach ($this->fieldcatalogconditions as $cond){
                    $conditions[$cond->AttributeName] = $cond;
                }
                foreach ($this->condition as $key => $value){
                    $criteria = [
                        'IdFieldCatalogSource' => $this->Id,
                        'AttributeName' => $value['key'],
                    ];
                    $cond = isset($conditions[$value['key']]) ? $conditions[$value['key']]:null;
                    if(empty($cond)){
                        $cond = new Fieldcatalogcondition();
                        $cond->attributes = $criteria;
                    } 
                    $cond->Value = $value['value'];
                    if(!$cond->save()){
                        $message = Yii::$app->customFunctions->getErrors($cond->errors);
                        $this->addError('Code', $message);
                        throw new Exception($message, 94000);
                    } else {
                        $cond->refresh();
                        array_push($usedcondition, $cond->Id);
                    }
                }
            }
            Fieldcatalogcondition::deleteAll(['AND','IdFieldCatalogSource = :idmodel',['NOT IN', 'Id', $usedcondition]],[':idmodel' => $this->Id]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModels() 
    { 
        $models = Registredmodel::find()->all();
        return ArrayHelper::map($models, 'Id', 'Name');
    }
    
    
    private function _loadModel(){
        try {
            $this->rmodel = Registredmodel::findOne(['Id' => $this->IdRegistredModel]);
            $this->model = $this->rmodel->getModel();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributesForm($i = 0){
        try {
            $this->refresh();
            $this->_loadModel();
            $id = $i.'.'. (int)$this->Id;
            $input = Html::hiddenInput(Field::tableName()."[model][$this->Id]", $this->Id,['id'=>'model-'.$id]);
            $label = Html::label('Modelo','model-'.$id, []);
            $field = Html::label($this->rmodel->Name, null, ['class' => 'form-control disabled']);
            $divSource = Html::tag('div', $input.$label.$field, ['class' => 'col-3 modelsource-row']);
            $aliasLabel = Html::label($this->getAttributeLabel('TableAlias'),'model-alias-'.$id);
            $aliasInput = Html::textInput('model-alias-'.$id, null, ['id' => 'model-alias-'.$id, 'class' => 'form-control']);
            $divAlias = Html::tag('div',$aliasLabel.$aliasInput,['class' => 'col-2']);
            
            $stringLabel = Html::label($this->getAttributeLabel('RelationString'),'model-string-'.$id,[]);
            $stringInput = Html::textInput('model-string-'.$id,null,['class' => 'form-control']);
            $divString = Html::tag('div', $stringLabel.$stringInput, ['class' => 'col-3']);
            $inputModel = $divSource.$divAlias.$divString;
            $condition = $this->_getModelAttributeForm();
            $condContainer = Html::tag('div',$condition, ['class' => 'card-body']);
            $modelContainerHeader = Html::tag('div',$inputModel, ['class' => 'card-header']);
            $modelContainerBody = Html::tag('div',$condContainer, ['class' => 'card-body']);
            $card = Html::tag('div',$modelContainerHeader.$modelContainerBody,['class' => 'card']);
            $container = Html::tag('div', $card, ['class' => 'col-12']);
            
            return $container;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getModelAttributeForm($selectValue = null, $inputValue = null, $parentId = null){
        try {
            $response = [];
            $attributes = $this->rmodel->getModelAttributes();
            $class = StringHelper::basename(Field::class);
            $table = Field::tableName();
            $partsId = $this->lastId ? explode('-', $this->lastId) : [];
            $c = !empty($partsId) ? ( (int) end($partsId) + 1):1;
            $selectId = $table.'-condition-select-'.$c;
            
            $labelSelect = Html::label('Llave',$selectId);
            $id = $c.'.'. $this->Id;
            $inputSelect = Html::dropDownList($class."[model][$id][condition][$c][key]", $selectValue, $attributes, ['class' => 'form-control','id' => $selectId]);
            $divSelect = Html::tag('div', $labelSelect.$inputSelect, ['class' => 'col-6 condition']);
            $response[] = $divSelect;
            
            $inputId = $table.'-condition-input-'.$c;
            $label = Html::label('Valor',$inputId);
            $input = Html::textInput($class."[condition][$c][value]", $inputValue, ['class' => 'form-control','id' => $inputId]);
            $div = Html::tag('div', $label.$input, ['class' => 'col-5 condition']);
            $response[] = $div;
            
            $labelButton = Html::label('&nbsp;','btn-'.$c);
            $button = Html::a('<i class="fas fa-times fa-1x"></i>', "javascript:removeCond($c);",['class' => 'btn btn-remove-cond']);
            $divButton = Html::tag('div', $labelButton.$button, ['class' => 'col-1 condition','id' => 'btn-'.$c]);
            $response[] = $divButton;
            
            return Html::tag('div', $divSelect.$div.$divButton, ['class' => 'condition-row']);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
