<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use Exception;

/**
 * This is the model class for table "extendedmodelfieldgroup".
 *
 * @property int $Id
 * @property int $IdExtendedModelKey
 * @property string $Name
 * @property int $IdState
 * @property int $Sort
 * @property int $VisibleContainer
 * @property string $Description
 *
 * @property Extendedmodelkey $extendedModelKey
 * @property State $state
 * @property Extendedmodelfield[] $extendedmodelfields
 */
class Extendedmodelfieldgroup extends \yii\db\ActiveRecord
{
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    const SORT_DEFAULT_VALUE = 1;
    const DEFAULT_GROUP_NAME = 'Default Group';
    const VISIBLE_CONTAINER_ENABLED = 1;
    const VISIBLE_CONTAINER_DISABLED = 0;
    
    public $htmlList = null;
    private $controllerName = null;
    public $create = false;
    public $update = false;
    public $delete = false;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelfieldgroup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelKey', 'Name', 'IdState'], 'required'],
            [['IdExtendedModelKey', 'IdState', 'Sort','VisibleContainer'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['IdExtendedModelKey'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelkey::class, 'targetAttribute' => ['IdExtendedModelKey' => 'id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'id']],
            ['Sort','default','value' => self::SORT_DEFAULT_VALUE],
            ['Sort','compare','compareValue' => self::SORT_DEFAULT_VALUE, 'operator' => '>=','type' => 'number','message' => '{attribute} debe ser mayor o igual que '.self::SORT_DEFAULT_VALUE],
            ['VisibleContainer','default','value' => self::VISIBLE_CONTAINER_DISABLED],
            ['VisibleContainer','in','range' => [self::VISIBLE_CONTAINER_DISABLED, self::VISIBLE_CONTAINER_ENABLED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelKey' => 'Llave',
            'Name' => 'Nombre',
            'IdState' => 'Estado',
            'Sort' => 'Orden',
            'VisibleContainer' => 'Mostrar Contenedor',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelKey()
    {
        return $this->hasOne(Extendedmodelkey::class, ['Id' => 'IdExtendedModelKey']);
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
    public function getExtendedmodelfields()
    {
        return $this->hasMany(Extendedmodelfield::class, ['IdExtendedModelFieldGroup' => 'Id']);
    }
    
    public function getHTMLList(){
        try {
            $this->controllerName = 'extendedmodelkey';
            $this->create = Yii::$app->customFunctions->userCan($this->controllerName.'Create');
            $this->update = Yii::$app->customFunctions->userCan($this->controllerName.'Update');
            $this->delete = Yii::$app->customFunctions->userCan($this->controllerName.'Delete');
            
            $this->htmlList = $this->_getHtmlChildren();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getHtmlChildren(){
        try {
            $table = "";
            $options = self::findAll(['IdExtendedModelKey' => $this->IdExtendedModelKey]);
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
                            . "<td>".$opt->Name. "</td>"
                            . "<td>".$opt->Code. "</td>"
                            . "<td>".($opt->IdState ? $opt->state->Name:""). "</td>"
                            . "<td>"
                            .($this->create ? Html::a("<span style='margin-right:5pt'><i class='fas fa-plus-square fa-lg'></i></span>", "javascript:addCategory($opt->Id);", ['title'=>'Agregar Categoría hija']) :"")
                            .($this->update ? Html::a("<span style='margin-right:5pt'><i class='fas fa-pen-square fa-lg'></i></span>", "javascript:editCategory($opt->Id);", ['title'=>'Editar Categoría']) :"")
                            .(($this->create || $this->update) ? Html::a("<span style='margin-right:5pt'><i class='fas fa-th fa-lg'></i></span>", ['update', 'id' => $opt->Id], ['title'=>'Detalle Categoría']) :"")
                            .($this->delete ? Html::a("<span style='margin-left:10pt; margin-right:5pt'><i class='fas fa-trash-alt fa-lg'></i></span>", "javascript:deleteCategory($opt->Id);", ['title'=>'Eliminar Categoría']) :"")
                            . "</td>"
                            . "</tr>";
                    if(!empty($opt->incidentcategories)){
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
    
    private function _getNextSort(){
        try {
            $values = self::find()
                    ->where(['IdExtendedModelKey' => $this->IdExtendedModelKey])
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
    
}
