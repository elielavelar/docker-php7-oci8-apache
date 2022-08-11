<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use yii\web\JsExpression;
use kartik\widgets\SwitchInput;
use yii\widgets\MaskedInput;
use Exception;

/**
 * This is the model class for table "extendedmodelfield".
 *
 * @property int $Id
 * @property int $IdExtendedModelFieldGroup
 * @property int $IdField
 * @property string $CustomLabel
 * @property int $Required
 * @property int $Sort
 * @property string $CssClass
 * @property int $ColSpan
 * @property int $RowSpan
 * @property int $UseCustomMask
 * @property string $CustomMask
 * @property string $Description
 *
 * @property Field $field
 * @property Extendedmodelfieldgroup $extendedModelFieldGroup
 * @property Extendedmodelfieldvalue[] $extendedmodelfieldvalues
 */
class Extendedmodelfield extends \yii\db\ActiveRecord
{
    const REQUIRED_ENABLED = 1;
    const REQUIRED_DISABLED = 0;
    const SORT_DEFAULT_VALUE = 1;
    const ROWS_DEFAULT_VALUE = 1;
    const COLS_DEFAULT_VALUE = 6;
    const USE_CUSTOMMASK_ENABLED = 1;
    const USE_CUSTOMMASK_DISABLED = 0;
    
    public $idExtendedModel = null;
    private $_idInput = null;
    private $_idValue = null;
    private $_fieldName = null;
    private $_inputName = null;
    private $_input = [];
    private $_inputOptions = [];
    private $_containerClass = null;
    private $_extValue = null;
    public $columns = [];
    private $_model = null;
    private $_rmodel = null;
    private $_fieldjsValidation = null;
    private $form;
    private static $index = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelfield';
    }
    
    public function __construct($config = array()) {
        $this->columns = range(1, 12);
        return parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelFieldGroup', 'IdField'], 'required'],
            [['IdExtendedModelFieldGroup', 'IdField', 'Required', 'Sort', 'ColSpan', 'RowSpan', 'UseCustomMask'], 'integer'],
            [['Description'], 'string'],
            [['CustomLabel', 'CssClass', 'CustomMask'], 'string', 'max' => 100],
            [['IdExtendedModelFieldGroup'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelfieldgroup::class, 'targetAttribute' => ['IdExtendedModelFieldGroup' => 'Id']],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['IdField' => 'Id']],
            ['IdField','unique','when' => function($model){
                if($model->field->MultipleValue == Field::MULTIPLE_VALUES_TRUE){
                    $response = false;
                } else {
                    $search = Extendedmodelfield::find()
                            ->innerJoin(Extendedmodelfieldgroup::tableName().' b', Extendedmodelfield::tableName().'.IdExtendedModelFieldGroup = b.Id')
                            ->innerJoin(Extendedmodelkey::tableName().' c', 'b.IdExtendedModelKey = c.Id')
                            ->where([
                                'IdField' => $model->IdField,
                                'c.Id' => $model->extendedModelFieldGroup->IdExtendedModelKey
                            ]);
                    
                    !empty($model->Id) ? $search->andWhere(Extendedmodelfield::tableName().'.Id != :id',[':id' => $model->Id]) : null;
                    $count = $search->count();
                    $response = $count > 0;
                }
                return $response;
            },'message' => '{attribute} ya ha sido utilizado en esta Llave'],
            ['Required','in','range' => [self::REQUIRED_DISABLED, self::REQUIRED_ENABLED]],
            ['Required','default','value' => self::REQUIRED_DISABLED],
            ['Sort','default','value' => self::SORT_DEFAULT_VALUE],
            ['Sort','compare','compareValue' => self::SORT_DEFAULT_VALUE, 'operator' => '>=','type' => 'number','message' => '{attribute} debe ser mayor o igual que '.self::SORT_DEFAULT_VALUE],
            ['ColSpan','default','value' => self::COLS_DEFAULT_VALUE],
            ['ColSpan','in', 'range' => range(1, 12), 'message' => '{attribute} debe estar entre '.reset($this->columns).' y '. end($this->columns)],
            ['RowSpan','default','value' => self::ROWS_DEFAULT_VALUE],
            ['RowSpan','compare','compareValue' => 1, 'operator' => '>=','type' => 'number','message' => '{attribute} debe ser mayor o igual que 1'],
            ['UseCustomMask','default','value' => self::USE_CUSTOMMASK_DISABLED],
            ['UseCustomMask','in','range' => [self::USE_CUSTOMMASK_DISABLED, self::USE_CUSTOMMASK_ENABLED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelFieldGroup' => 'Grupo',
            'IdField' => 'Campo',
            'CustomLabel' => 'Etiqueta Personalizada',
            'Required' => 'Requerido',
            'Sort' => 'Orden',
            'CssClass' => 'Clase Css',
            'ColSpan' => 'Columnas',
            'RowSpan' => 'Filas',
            'Description' => 'Descripción',
            'UseCustomMask' => 'Usa Mascara Personalizada',
            'CustomMask' => 'Mascara Personalizada',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelFieldGroup()
    {
        return $this->hasOne(Extendedmodelfieldgroup::class, ['Id' => 'IdExtendedModelFieldGroup']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::class, ['Id' => 'IdField']);
    }
    
    public function getFields(){
        $ext = Extendedmodel::findOne(['Id' => $this->idExtendedModel]);
        $fields = Field::find()
                ->where(['KeyWord' => $ext->Id ? $ext->registredModel->KeyWord : null])
                ->orderBy(['Id' => SORT_ASC])
                ->all();
        return ArrayHelper::map($fields, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelfieldvalues()
    {
        return $this->hasMany(Extendedmodelfieldvalue::class, ['IdExtendedModelField' => 'Id']);
    }
    
    public function getColumns(){
        $columns = [];
        foreach ($this->columns as $col){
            $columns[] = ['Key' => $col];
        }
        return ArrayHelper::map($columns, 'Key', 'Key');
    }
    
    private function _getNextSort(){
        try {
            $values = self::find()
                    ->where(['IdExtendedModelFieldGroup' => $this->IdExtendedModelFieldGroup])
                    ->max('Sort');
            $this->Sort = (int)$values + 1;
        } catch (Exception $ex) {
            throw $ex;
        }
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
    
    /*
     * Field Id is composed by 
     * [IdExtendedModelField][IndexField][IdRecord]
     * IdExtendedModelField - Id of Extendedmodel Field
     * IndexField - Index number field
     */
    public function getFormField($id = null, \kartik\form\ActiveForm $form = null ){
        try {
            $this->form = $form;
            $this->_idValue = (int) $id;
            $this->_rmodel = $this->extendedModelFieldGroup->extendedModelKey->extendedModel->registredModel;
            $this->_model = $this->_rmodel->getModel();
            $this->_idInput = $this->_model->tableName().'-extendedmodelfields-'.$this->Id.'_'.self::$index .'_'.$this->_idValue;
            $this->_fieldName = StringHelper::basename(self::class)."[$this->Id][". self::$index."][$this->_idValue]";
            $this->_inputName = StringHelper::basename($this->_rmodel->KeyWord)."[".StringHelper::basename(self::class)."][$this->Id][".self::$index."][$this->_idValue]";
            $this->_extValue = $this->_getExtendedValue();
            $label = (!empty($this->CustomLabel) ? $this->CustomLabel : $this->field->Name);
            $htmlField = Html::label($label, $this->_idInput, ['class' => 'control-label'.($this->Required ? ' has-star':'')]) ;
            if($this->field->HasCatalog){
                $htmlField .= $this->field->EnabledModelSource ? $this->_getModelSourceFormField() : $this->_getCatalogFormField();
                $htmlField .= $this->field->CombinationValue ? $this->_getCombinationFormField(): null;
            } elseif($this->field->UseMask){
                $htmlField .= $this->_getMaskedFormField();
            } else {
                switch ($this->field->type->Code){
                    case Field::TYPE_FIELD_SWITCH :
                        $htmlField .= $this->_getSwitchFormField();
                        break;
                    case Field::TYPE_FIELD_MASK :
                        $htmlField .= $this->_getMaskedFormField();
                        break;
                    default :
                        $htmlField .= $this->_getSimpleFormField();
                        break;
                }
            }
            $this->_containerClass = 'field-'.$this->_idInput;
            $expression = 'function(attribute, value, messages, deferred, $form) {'
                    . "yii.validation.required(value, messages, {'message': 'Campo $label no puede quedar vacío'});"
                    . "}";
            $this->_fieldjsValidation = [
                'id' => $this->_idInput,
                'name' => $this->_fieldName,
                'container' => '.'.$this->_containerClass,
                'input' => '#'.$this->_idInput,
                #'error'=> '.help-block',
                'validate' => $this->Required == self::REQUIRED_ENABLED ? new JsExpression($expression) : null
            ];
            if($this->form){
                $htmlFieldGroup = $htmlField;
            } else {
                $htmlField .= Html::tag('div',null,['class' => 'help-block']);
                $htmlFieldGroup = Html::tag('div', $htmlField, ['class' => 'form-group '.$this->_containerClass.($this->Required == self::REQUIRED_ENABLED ? ' highlight-addon required':'')]);
            }
            self::$index++;
            return Html::tag('div', $htmlFieldGroup, ['class' => 'col-'.$this->ColSpan.' extfield']);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getSimpleFormField(){
        try {
            return ($this->field->UseMask == Field::USE_MASK_ENABLED || $this->UseCustomMask == self::USE_CUSTOMMASK_ENABLED) ? $this->_getMaskedFormField() : 
                Html::textInput($this->_inputName, (!empty($this->_extValue) ? ($this->_extValue->CustomValue ? $this->_extValue->Value : $this->_extValue->IdFieldCatalog): null), ['id' => $this->_idInput, 'class' => 'form-control']);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getMaskedFormField(){
        try {
            $this->_input = [
                'name' => $this->_inputName,
                'options' => ['id' => $this->_idInput, 'class' => 'form-control'],
            ];
            ($this->UseCustomMask == self::USE_CUSTOMMASK_ENABLED ) ? $this->_setCustomMask() : $this->_setFieldMask();
            ($this->field->MultipleValue == Field::MULTIPLE_VALUES_TRUE && !empty($this->form)) ? $this->_setMultipleField() : null;
            return $this->form ? $this->form->field($this->_model, StringHelper::basename(self::class)."[$this->Id][".self::$index."][$this->_idValue]", $this->_inputOptions)->widget(MaskedInput::class, $this->_input)->label(false) : MaskedInput::widget($this->_input);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setMultipleField(){
        try {
            $this->_inputOptions['addon'] = [
                'append' => [
                    'content' => Html::button('<i class="fas fa-plus"></i>',[ 'class' => 'btn btn-primary btn-sm btn-add-field', 'data-toggle' => 'tooltip',]),
                    'asButton' => true,
                ]
            ];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setFieldMask(){
        try {
            $this->_input = array_merge($this->_inputOptions, $this->field->getMaskConf());
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setCustomMask(){
        try {
            if(!(strpos($this->CustomMask, 'alias:') === false)){
                    $this->_input['mask'] = $this->CustomMask ;
            } else {
                if(strpos($this->CustomMask, 'alias:') !== false){
                    $alias = explode('alias:', $this->CustomMask);
                    $this->_input['clientOptions'] = [
                        'alias' => implode('',$alias),
                    ];

                } else {
                    $this->_input['mask'] = $this->CustomMask;
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getModelSourceFormField(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getCatalogFormField(){
        try {
            $value = (!empty($this->_extValue) ? ($this->_extValue->CustomValue ? $this->_extValue->Value : $this->_extValue->IdFieldCatalog): null);
            $items = ArrayHelper::map($this->field->fieldscatalogs, 'Id', 'Name');
            return Html::dropDownList($this->_inputName, $value, $items, ['id' => $this->_idInput, 'prompt'=>'-Seleccione Valor-', 'class' => 'form-control'.($this->Required == self::REQUIRED_ENABLED ? ' required':''),'aria-required' => ($this->Required == self::REQUIRED_ENABLED ? 'true':'false')]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getCombinationFormField(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getSwitchFormField(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getExtendedValue(){
        try {
            return null;
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getFieldValidation(){
        return $this->_fieldjsValidation;
    }
    
    public function setIndex(int $index = 0){
        self::$index = $index;
    }
}
