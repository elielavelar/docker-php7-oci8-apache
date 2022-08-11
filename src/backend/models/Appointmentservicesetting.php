<?php

namespace backend\models;

use Yii;
use common\models\State;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "appointmentservicesetting".
 *
 * @property integer $Id
 * @property integer $IdServiceCentre
 * @property integer $IdDay
 * @property integer $IdHour
 * @property integer $IdState
 * @property integer $Quantity
 * 
 * @property State $state 
 */
class Appointmentservicesetting extends \yii\db\ActiveRecord
{
    public $weekdays;
    public $start_time;
    public $end_time;
    
    const SCENARIO_BATCH = 'batch';
    const DEFAULT_STATE  = 'ACT';
    const INACTIVE_STATE  = 'INA';
    
    const NORMAL_START_MORNING_SCHEDULE = 'LVMI';
    const NORMAL_END_MORNING_SCHEDULE = 'LVMF';
    const NORMAL_START_AFTERNOON_SCHEDULE = 'LVVI';
    const NORMAL_END_AFTERNOON_SCHEDULE = 'LVVF';
    const WEEKEND_START_SCHEDULE = 'SABI';
    const WEEKEND_END_SCHEDULE = 'SABF';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointmentservicesetting';
    }
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_BATCH] = ['IdServiceCentre','IdDay','IdHour','Value'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdServiceCentre', 'IdDay', 'IdState'], 'required','message'=>'Campo {attribute} no puede quedar vacío'],
            [['IdServiceCentre', 'IdDay','IdHour', 'IdState', 'Quantity'], 'integer'],
            [['IdState'], 'default','value'=>  State::findOne(['KeyWord'=>'Appointmentservicesetting','Code'=>  self::DEFAULT_STATE])->Id],
            [['IdHour'], 'unique', 'targetAttribute' => ['IdServiceCentre','IdDay' ,'IdHour'], 'message' => 'Ya existe configuración para el horario {value} de ese día'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdServiceCentre' => 'Duicentro',
            'IdDay' => 'Día',
            'IdHour' => 'Hora',
            'IdState' => 'Estado',
            'Quantity' => 'Cantidad',
        ];
    }
    /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getState() 
   { 
       return $this->hasOne(State::className(), ['Id' => 'IdState']); 
   } 
}
