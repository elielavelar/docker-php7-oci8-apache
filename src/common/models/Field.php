<?php

namespace common\models;

use Yii;
use common\models\Catalogdetail;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "fields".
 *
 * @property int $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property int $IdType
 * @property int $IdState
 * @property int $HasCatalog
 * @property int $UseMask
 * @property int $EnabledCustomMask
 * @property int $IdInputMask
 * @property string $DefaultMask
 * @property string $Value
 * @property int $MultipleValue
 * @property int $CombinationValue
 * @property int $EnabledModelSource
 * @property string $AttributeSourceName
 * @property string $Description
 *
 * @property Extendedmodelfield[] $extendedmodelfields
 * @property Fieldcatalogsource[] $fieldcatalogsources
 * @property Catalogdetail $inputMask
 * @property State $state
 * @property Type $type
 * @property Fieldcatalog[] $fieldscatalogs
 * @property Modelfielddetail[] $modelfielddetails
 */
class Field extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    const HAS_CATALOG_FALSE = 0;
    const HAS_CATALOG_TRUE = 1;
    const MULTIPLE_VALUES_FALSE = 0;
    const MULTIPLE_VALUES_TRUE = 1;
    const USE_MASK_ENABLED = 1;
    const USE_MASK_DISABLED = 0;
    const MODEL_SOURCE_ENABLED = 1;
    const MODEL_SOURCE_DISABLED = 0;
    const CUSTOM_MASK_ENABLED = 1;
    const CUSTOM_MASK_DISABLED = 0;
    const INPUT_MASK_CODE = 'MASK';
    const TYPE_FIELD_SWITCH = 'SWITCH';
    const TYPE_FIELD_MASK = 'MASK';

    public $customvalue = null;
    private $rmodel = null;
    private $model = null;
    private $idRegistredModel = null;
    private $condition = [];
    public $conditionform = [];
    public $modelAttributes = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code', 'IdType', 'IdState'], 'required'],
            [['IdType', 'IdState', 'HasCatalog', 'MultipleValue', 'UseMask', 'EnabledCustomMask', 'IdInputMask', 'MultipleValue', 'CombinationValue', 'EnabledModelSource'], 'integer'],
            [['Description'], 'string'],
            [['Code'], 'unique', 'targetAttribute' => ['KeyWord', 'Code'], 'message' => 'Ya existe el Código {value} para la llave ingresada'],
            [['Name', 'KeyWord', 'Code', 'Value'], 'string', 'max' => 50],
            [['DefaultMask'], 'string', 'max' => 250],
            ['DefaultMask','required','when' => function($model){
                return $model->EnabledCustomMask == self::CUSTOM_MASK_ENABLED && empty($model->DefaultMask);
            },'message' => '{attribute} no puede quedar vacío'],
            ['IdInputMask','required','when' => function($model){
                return $model->UseMask == self::USE_MASK_ENABLED && $model->EnabledCustomMask == self::CUSTOM_MASK_DISABLED && empty($model->IdInputMask);
            },'message' => '{attribute} no puede quedar vacío'],
            [['AttributeSourceName'], 'string', 'max' => 100],
            [['KeyWord', 'Code'], 'unique', 'targetAttribute' => ['KeyWord', 'Code']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['IdInputMask'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetail::class, 'targetAttribute' => ['IdInputMask' => 'Id']],
            ['HasCatalog', 'default', 'value' => self::HAS_CATALOG_FALSE],
            [['HasCatalog'],'in','range'=>[self::HAS_CATALOG_FALSE, self::HAS_CATALOG_TRUE]],
            ['MultipleValue', 'default', 'value' => self::MULTIPLE_VALUES_FALSE],
            [['MultipleValue'],'in','range'=>[self::MULTIPLE_VALUES_FALSE, self::MULTIPLE_VALUES_TRUE]],
            ['UseMask','default','value' => self::USE_MASK_DISABLED],
            ['UseMask','in','range' => [self::USE_MASK_DISABLED,self::USE_MASK_ENABLED]],
            ['EnabledModelSource','default','value' => self::MODEL_SOURCE_DISABLED],
            ['EnabledModelSource','in','range' => [self::MODEL_SOURCE_DISABLED, self::MODEL_SOURCE_ENABLED]],
            ['EnabledCustomMask','default','value' => self::CUSTOM_MASK_DISABLED],
            ['EnabledCustomMask','in','range' => [self::CUSTOM_MASK_DISABLED, self::CUSTOM_MASK_ENABLED]],
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
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'HasCatalog' => 'Tiene Catálogo',
            'UseMask' => 'Usa Máscara',
            'EnabledCustomMask' => 'Usar Máscara Personalizada',
            'IdInputMask' => 'Máscara',
            'DefaultMask' => 'Máscara Personalizada',
            'Value' => 'Valor',
            'customvalue' => 'Valor',
            'MultipleValue' => 'Multiples Valores',
            'EnabledModelSource' => 'Habilitar Modelo Relacionado',
            'AttributeSourceName' => 'Atributo Relacionado',
            'CombinationValue' => 'Valor Combinado',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelfields()
    {
        return $this->hasMany(Extendedmodelfield::class, ['IdField' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldcatalogsources()
    {
        return $this->hasMany(Fieldcatalogsource::class, ['IdField' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInputMask()
    {
        return $this->hasOne(Catalogdetail::class, ['Id' => 'IdInputMask']);
    }

    public function getInputMasks(){
        try {
            $options = Catalogdetail::find()
                ->joinWith('state b', false)
                ->innerJoin(Catalogversion::tableName().' c', Catalogdetail::tableName().'.IdCatalogVersion = c.Id')
                ->innerJoin(Catalog::tableName().' d', 'c.IdCatalog = d.Id')
                ->where([
                    Catalogdetail::tableName().'.KeyWord' => StringHelper::basename(self::class),
                    'b.Code' => self::STATUS_ACTIVE,
                    'd.KeyWord' => StringHelper::basename(self::class),
                    'd.Code' => self::INPUT_MASK_CODE,
                ])
                ->asArray()
                ->all();
            return ArrayHelper::map($options, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
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
            $options = State::find()
                ->where(['KeyWord' => StringHelper::basename(self::class)])
                ->asArray()
                ->all();
            return ArrayHelper::map($options, 'Id', 'Name');
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
            $options = Type::find()
                ->joinWith('state b', false)
                ->where([
                    'type.KeyWord' => StringHelper::basename(self::class),
                    'b.Code' => self::STATUS_ACTIVE,
                ])
                ->orderBy([Type::tableName().'.Sort' => SORT_ASC])
                ->asArray()
                ->all();
            return ArrayHelper::map($options, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldscatalogs()
    {
        return $this->hasMany(Fieldcatalog::class, ['IdField' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelfielddetails()
    {
        return $this->hasMany(Modelfielddetail::class, ['IdField' => 'Id']);
    }

    public function beforeSave($insert) {
        $this->Value = $this->EnabledModelSource ? $this->customvalue : $this->Value;
        $this->Value = empty($this->Value) ? null: $this->Value;
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_saveConditions();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind() {
        $this->customvalue = $this->EnabledModelSource ? $this->Value : null;
        return parent::afterFind();
    }

    public function setCondition($condition = []){
        $this->condition = $condition;
    }

    private function _saveConditions(){
        try {
            foreach ($this->condition as $cond){

            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function loadModelAttributesForm(){
        try {
            $response = [];
            $i = 0;
            foreach ($this->fieldcatalogsources as $model){
                $response[] = $model->getModelAttributesForm($i);
                $i++;
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getMaskConf() : array {
        try {
            $useAlias = false;
            $alias = null;
            $mask = [];
            $maskOptions = [];
            if($this->EnabledCustomMask == self::CUSTOM_MASK_ENABLED){
                if(strpos($this->DefaultMask, 'alias:') !== false){
                    $maskOptions['clientOptions'] = [
                        'alias' => implode('',explode('alias:', $this->DefaultMask))
                    ];
                } else {
                    $maskOptions['mask'] = $this->DefaultMask;
                }
            } else {
                if(empty($this->IdInputMask)){
                   throw new Exception('Máscara no Definida', 98001); 
                } 
                if(count($this->inputMask->catalogdetailvalues) == 0 ){
                   throw new Exception('Máscara no Definida', 98001); 
                } else {
                    foreach ($this->inputMask->catalogdetailvalues as $value){
                        if(strpos($value->Value, 'alias:') !== false){
                            $useAlias = true;
                            $alias = implode('',explode('alias:', $value->Value));
                            break;
                        } else {
                            array_push($mask, $value->Value);
                        }
                    }
                    if($useAlias){
                        $maskOptions['clientOptions'] = [
                            'alias' => $alias
                        ];
                    } else {
                        $maskOptions['mask'] = $mask;
                    }
                }
            }
            return $maskOptions;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
