<?php

namespace backend\models;

use Yii;
use common\models\Servicecentre;
use common\models\State;

use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use kartik\helpers\Html;
use Exception;

/**
 * This is the model class for table "infrastructurerequirementtype".
 *
 * @property int $Id
 * @property string $Name
 * @property string $Code
 * @property int $IdState
 * @property int $IdServiceCentre
 * @property int $IdParent
 * @property string $Description
 *
 * @property Infrastructurerequirementtype $parent
 * @property Infrastructurerequirementtype[] $infrastructurerequirementtypes
 * @property Servicecentre $serviceCentre
 * @property State $state
 */
class Infrastructurerequirementtype extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    const DEFAULT_PARENT = null;
    
    public $controllerName = null;
    private $level = 0;
    public $htmlList = "";
    public $create = FALSE;
    public $update = FALSE;
    public $delete = FALSE;
    private $rowcolor = ['','bg-success','bg-warning','bg-info','bg-primary','bg-danger'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infrastructurerequirementtype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Code', 'IdState'], 'required'],
            [['IdState', 'IdServiceCentre', 'IdParent'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['Code'], 'string', 'max' => 20],
            [['Code'], 'unique'],
            [['IdParent'], 'exist', 'skipOnError' => true, 'targetClass' => Infrastructurerequirementtype::className(), 'targetAttribute' => ['IdParent' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::className(), 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
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
            'Code' => 'Código',
            'IdState' => 'Estado',
            'IdServiceCentre' => 'Departamento',
            'IdParent' => 'Padre',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Infrastructurerequirementtype::className(), ['Id' => 'IdParent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructurerequirementtypes()
    {
        return $this->hasMany(Infrastructurerequirementtype::className(), ['IdParent' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::className(), ['Id' => 'IdServiceCentre']);
    }
    
    public function getServiceCentres(){
        $model = Servicecentre::find()
                    ->joinWith('state b')
                    ->where([
                        'b.Code' => Servicecentre::STATE_ACTIVE,
                    ])->all();
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $model = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($model, 'Id', 'Name');
    }
    
    
    public function getHTMLList($idparent = null){
        try {
            $this->create = Yii::$app->customFunctions->userCan($this->controllerName.'Create');
            $this->update = Yii::$app->customFunctions->userCan($this->controllerName.'Update');
            $this->delete = Yii::$app->customFunctions->userCan($this->controllerName.'Delete');
            
            $this->htmlList = $this->_getHtmlChildren($idparent);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getChildren($idParent = NULL){
        try {
            $this->level++;
            return self::find()->where(['IdParent'=> $idParent])->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getHtmlChildren($idParent = NULL, $level = 0){
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
                            . "<td>".
                            ($this->update ? Html::a($opt->Name, "javascript:editDetail($opt->Id);", ['title'=>'Agregar Detalle']) : $opt->Name)
                            . "</td>"
                            . "<td>".$opt->Code. "</td>"
                            . "<td>".($opt->IdParent ? $opt->parent->Name:""). "</td>"
                            . "<td>".($opt->IdServiceCentre ? $opt->serviceCentre->Name:""). "</td>"
                            . "<td>".($opt->IdState ? $opt->state->Name:""). "</td>"
                            . "<td>"
                            .($this->create ? Html::a("<span style='margin-right:5pt'><i class='fas fa-plus-square fa-lg'></i></span>", "javascript:addDetail($opt->Id);", ['title'=>'Agregar Detalle']) :"")
                            .($this->update ? Html::a("<span style='margin-right:5pt'><i class='fas fa-pen-square fa-lg'></i></span>", "javascript:editDetail($opt->Id);", ['title'=>'Editar Detalle']) :"")
                            .($this->delete ? '|'. Html::a("<span style='margin-left:10pt; margin-right:5pt'><i class='fas fa-trash-alt fa-lg'></i></span>", "javascript:deleteDetail($opt->Id);", ['title'=>'Eliminar Detalle']) :"")
                            . "</td>"
                            . "</tr>";
                    if(!empty($opt->infrastructurerequirementtypes)){
                        $table .= $this->_getHtmlChildren($opt->Id, ($level + 1));
                    }
                }
            } else {
                $table .= "<tr>"
                        . "<td colspan='7'>No se encontraron datos</td>"
                        . "</tr>";
            }
            return $table;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
