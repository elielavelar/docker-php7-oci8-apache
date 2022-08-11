<?php

namespace common\models;

use Yii;
use common\models\State;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
/**
 * This is the model class for table "type".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property string $Value
 * @property integer $Sort
 * @property integer $IdState
 * @property string $Description
 *
 * @property State $state
 */
class Type extends \common\models\CustomActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    
    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_SEARCH = 'search';
    const SCENARIO_UPDATE = 'update';
    const DEFAULT_SORT = 1;

    public $create ;
    public $update ;
    public $delete ;
    public $view ;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'type';
    }

    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = ['Id','Name','KeyWord','Code'
            ,'Value','IdState','Description','Sort'
        ];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_SEARCH];
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code', 'IdState','Sort'], 'required'],
            [['IdState','Sort'], 'integer'],
            [['Name', 'KeyWord'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 20],
            [['Value'], 'string', 'max' => 100],
            [['Description'], 'string', 'max' => 1000],
            [['Code'], 'unique', 'targetAttribute' => ['KeyWord', 'Code'], 'message' => 'Ya existe el Código {value} para la llave ingresada'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            ['Sort','default','value' => self::DEFAULT_SORT],
            ['Sort','compare','compareValue' => self::DEFAULT_SORT, 'operator' => '>=','type' => 'number','message' => '{attribute} debe ser mayor o igual que '.self::DEFAULT_SORT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'KeyWord' => 'Llave',
            'Code' => 'Código',
            'Value' => 'Valor',
            'Orden' => 'Orden',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        try {
            $droptions = State::findAll(['KeyWord'=>StringHelper::basename(self::className())]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    public function afterFind() {
        if(($this->scenario == self::SCENARIO_DEFAULT) && StringHelper::basename(Yii::getAlias('@app')) != 'console'){
            $this->create = \Yii::$app->customFunctions->userCan(self::tableName().'Create');
            $this->update = \Yii::$app->customFunctions->userCan(self::tableName().'Update');
            $this->delete = \Yii::$app->customFunctions->userCan(self::tableName().'Delete');
            $this->view = \Yii::$app->customFunctions->userCan(self::tableName().'View');
        }
        return parent::afterFind();
    }
}
