<?php

namespace common\models;

use Yii;
use common\models\Type;
use common\models\Field;
use kartik\helpers\Html;
use common\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "catalogdetailvalue".
 *
 * @property int $Id
 * @property int $IdCatalogDetail
 * @property int $IdDataType
 * @property int $IdValueType
 * @property int $Sort
 * @property int $IdParentValue
 * @property int $EnabledKeyWord
 * @property string $KeyWord
 * @property string $Value
 * @property string $Description
 *
 * @property Catalogdetail $catalogDetail
 * @property Catalogdetailvalue $parent
 * @property Catalogdetailvalue[] $children
 * @property Type $dataType
 * @property Type $valueType
 */
class Catalogdetailvalue extends CustomActiveRecord
{
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    const SORT_DEFAULT_VALUE = 1;
    const KEYWORD_ENABLED = 1;
    const KEYWORD_DISABLED = 0;
    
    private $level = 0;
    public $create = false;
    public $update = false;
    public $delete = false;
    public $controllerName =  'catalogdetail';
    private $rowcolor = ['','bg-success','bg-warning','bg-info','bg-primary','bg-danger'];
    public $htmlList = '';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalogdetailvalue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdCatalogDetail', 'IdDataType', 'IdValueType', 'Value'], 'required'],
            [['IdCatalogDetail', 'IdDataType', 'IdValueType','Sort','IdParentValue','EnabledKeyWord'], 'integer'],
            [['KeyWord'], 'string','max' => 50],
            [['Value'], 'string'],
            ['KeyWord', 'required', 'when' => function($model){
                return $model->EnabledKeyWord == Catalogdetailvalue::KEYWORD_ENABLED && empty($model->KeyWord);
            },'message' => '{attribute} no puede quedar vacío', 'enableClientValidation' => false, ],
            [['Description'], 'string', 'max' => 1000],
            [['IdCatalogDetail', 'Sort'], 'unique', 'targetAttribute' => ['IdCatalogDetail', 'Sort']],
            [['IdCatalogDetail'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetail::class, 'targetAttribute' => ['IdCatalogDetail' => 'Id']],
            [['IdDataType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdDataType' => 'Id']],
            [['IdValueType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdValueType' => 'Id']],
            [['IdParentValue'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['IdParentValue' => 'Id']],
            ['Sort' ,'default','value' => self::SORT_DEFAULT_VALUE],
            ['Sort','compare','compareValue' => self::SORT_DEFAULT_VALUE, 'operator' => '>=','type' => 'number','message' => '{attribute} debe ser mayor o igual que '.self::SORT_DEFAULT_VALUE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdCatalogDetail' => 'Detalle Catálogo',
            'IdDataType' => 'Tipo de Dato',
            'IdValueType' => 'Tipo de Valor',
            'EnabledKeyWord' => 'Habilitar Llave',
            'KeyWord' => 'Llave',
            'Value' => 'Valor',
            'Sort' => 'Orden',
            'IdParentValue' => 'Padre',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetail()
    {
        return $this->hasOne(Catalogdetail::class, ['Id' => 'IdCatalogDetail']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Catalogdetailvalue::class, ['Id' => 'IdParentValue']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Catalogdetailvalue::class, ['IdParentValue' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDataType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdDataType']);
    }

    public function getDataTypes(){
        $droptions = Type::find()
            ->joinWith('state b')
            ->where([
                'b.KeyWord' => StringHelper::basename(Type::class),
                'b.Code' => Type::STATUS_ACTIVE,
                Type::tableName().'.KeyWord' => StringHelper::basename(Field::class),
            ])
            ->orderBy([Type::tableName().'.Sort' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValueType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdValueType']);
    }

    public function getValueTypes(){
        $droptions = Type::find()
            ->joinWith('state b')
            ->where([
                'b.KeyWord' => StringHelper::basename(Type::class),
                'b.Code' => Type::STATUS_ACTIVE,
                Type::tableName().'.KeyWord' => StringHelper::basename(self::class),
            ])
            ->orderBy([Type::tableName().'.Sort' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
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
            $values = self::find();
            $values->where(['IdCatalogDetail' => $this->IdCatalogDetail]);
            $_values = $values->max('Sort');
            $this->Sort = (int)$_values + 1;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getHTMLList($idparent = null) {
        try {
            $this->create = Yii::$app->customFunctions->userCan($this->controllerName.'Create');
            $this->update = Yii::$app->customFunctions->userCan($this->controllerName.'Update');
            $this->delete = Yii::$app->customFunctions->userCan($this->controllerName.'Delete');
            
            $this->htmlList = $this->_getHtmlChildren($idparent);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getChildren($idParent = null){
        try {
            $this->level++;
            return self::find()->where(['IdParentValue'=> $idParent , 'IdCatalogDetail' => $this->IdCatalogDetail])->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getHtmlChildren($idParent = null, $level = 0){
        try {
            $table = "";
            $options = $this->_getChildren($idParent);
            if(!empty($options)){
                foreach ($options as $opt){
                    $this->level = $level;
                    $table .= "<tr class='".(isset($this->rowcolor[$level]) ? $this->rowcolor[$level]:"")."'>";
                    $table .= "<td>";
                    for ($j = 0; $j < $level; $j++){
                        $table .= "<i class='fas fa-angle-double-right fa-sm'></i> ";
                    }
                    $table .= $opt->Id
                            . "</td>"
                            . "<td>".$opt->Sort. "</td>"
                            . "<td>".($opt->IdDataType ? $opt->dataType->Name:''). "</td>"
                            . "<td>".($opt->IdValueType ? $opt->valueType->Name:''). "</td>"
                            . "<td>".$opt->Value. "</td>"
                            . "<td>"
                            .($this->create ? Html::a("<span style='margin-right:5pt'><i class='fas fa-plus-square fa-lg'></i></span>", "javascript:addDetail($opt->Id);", ['title'=>'Agregar Valor Hijo']) :"")
                            .($this->update ? Html::a("<span style='margin-right:5pt'><i class='fas fa-pen-square fa-lg'></i></span>", "javascript:editDetail($opt->Id);", ['title'=>'Editar Valor']) :"")
                            .($this->delete ? Html::a("<span style='margin-left:10pt; margin-right:5pt'><i class='fas fa-trash-alt fa-lg'></i></span>", "javascript:deleteDetail($opt->Id);", ['title'=>'Eliminar Valor']) :"")
                            . "</td>"
                            . "</tr>";
                    if(!empty($opt->children)){
                        $table .= $this->_getHtmlChildren($opt->Id, ($level + 1));
                    }
                }
            } else {
                $table .= "<tr>"
                        . "<td colspan='5'>No se encontraron datos</td>"
                        . "</tr>";
            }
            return $table;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
