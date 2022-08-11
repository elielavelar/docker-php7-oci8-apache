<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use yii\web\JsExpression;
use backend\models\Appointmentservicesetting;
use backend\models\Settingdetail;
use common\models\traits\Servicecentreservicetrait;
use Exception;

/**
 * This is the model class for table "servicecentre".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $ShortName
 * @property string $ServiceName
 * @property string $Code
 * @property integer $MBCode
 * @property integer $IdCountry
 * @property integer $IdZone
 * @property integer $IdState
 * @property integer $IdType
 * @property integer $EnabledMonitoring
 * @property string $Address
 * @property string $Phone
 *
 * @property State $state
 * @property Zone $zone
 * @property Type $type
 * @property Country $country
 * @property Appointmentservicesetting[] $appointmentservicesettings
  */
class Servicecentre extends \yii\db\ActiveRecord
{
    use traits\Servicecentretrait;
    //use Servicecentreservicetrait;
    const _FILE_PATH_ = '@backend/web/attachments';
    const _PATH_ATTACHMENTS_ = 'attachments';
    public $nameCountry = "";
    public $appointmentsetting = NULL;
    public $disabledhours = NULL;
    public $suggest = [];
    public $suggestList = '';
    
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    
    const TYPE_DUISITE = 'DUISITE';
    const TYPE_OFFICE = 'OFFC';
    const TYPE_CONSULATE  = 'CONS';
    const TYPE_DATACENTER  = 'DATA';
    
    const MONITORING_ENABLED = 1;
    const MONITORING_DISABLED = 0;
    
    const SUGGESTION_CODE = 'SGST';
    
    const PROFILE_CHIEF_SERVICECENTRE_DUISITE = 'JFDC';
    const PROFILE_AUXCHIEF_SERVICECENTRE_DUISITE = 'AUXJF';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'servicecentre';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'Code','MBCode','ShortName', 'IdCountry', 'IdState', 'IdType','IdZone'], 'required'],
            [['MBCode', 'IdCountry', 'IdState', 'IdType', 'IdZone','EnabledMonitoring'], 'integer'],
            [['Address'], 'string'],
            [['MBCode'],'unique','message' => 'Código MB {value} ya existe'],
            [['Code'],'unique','message' => 'Código {value} ya existe'],
            [['Name','ShortName'], 'string', 'max' => 50],
            [['ServiceName'], 'string', 'max' => 250],
            [['Code'], 'string', 'max' => 10],
            [['Phone'], 'string', 'max' => 15],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['IdCountry'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['IdCountry' => 'Id']],
            [['IdZone'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['IdZone' => 'Id']],
            [['EnabledMonitoring'],'in','range' => [self::MONITORING_DISABLED, self::MONITORING_ENABLED]],
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
            'ShortName' => 'Nombre Corto',
            'ServiceName' => 'Nombre Servicio',
            'Code' => 'Código',
            'MBCode' => 'Código Muhlbauer',
            'IdCountry' => 'País',
            'IdZone' => 'Zona',
            'IdState' => 'Estado',
            'IdType' => 'Tipo',
            'Address' => 'Dirección',
            'Phone' => 'Teléfono',
            'EnabledMonitoring' => 'Activar Monitoreo',
            'nameCountry' => 'País',
        ];
    }
    
    public function beforeSave($insert) {
        $model = self::find()->where(['MBCode'=>  $this->MBCode,'IdCountry'=>  $this->IdCountry]);
        if(!$this->isNewRecord){
            $model->andWhere(['<>','Id',  $this->Id]);
        }
        if($model->count() > 0){
            $this->addError('MBCode', 'Codigo Muhlbauer '.$this->MBCode." ya existe");
            return FALSE;
        }
        return parent::beforeSave($insert);
    }


    public function afterFind() {
        $this->nameCountry = $this->IdCountry ? $this->country->Name: NULL;
        return parent::afterFind();
    }
    
    public function afterSave($insert, $changedAttributes) {
        $this->_saveAppointmentSettings();
        return parent::afterSave($insert, $changedAttributes);
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
            $droptions = State::findAll(['KeyWord'=>StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
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
            $droptions = Type::findAll(['KeyWord'=>StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['Id' => 'IdCountry']);
    }
    
    public function getCountries(){
        try {
            $droptions = Country::findAll(['IdState'=>  State::findOne(['KeyWord'=> StringHelper::basename(Country::class),'Code'=>'ACT'])]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['Id' => 'IdZone']);
    }
    
    public function getZones(){
        try {
            $droptions = Zone::findAll(['IdState'=>  State::findOne(['KeyWord'=>  StringHelper::basename(Zone::class),'Code'=>  Zone::STATUS_ACTIVE])]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentservicesettings()
    {
        return $this->hasMany(Appointmentservicesetting::class, ['IdServiceCentre' => 'Id']);
    }
    
    private function _saveAppointmentSettings(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($this->appointmentsetting != NULL){
                
                
                $idstate = State::findOne(['KeyWord'=>'Appointmentservicesetting','Code'=> Appointmentservicesetting::DEFAULT_STATE])->Id;
                $disabledstate = State::findOne(['KeyWord'=>'Appointmentservicesetting','Code'=> Appointmentservicesetting::INACTIVE_STATE])->Id;
                
                #$data = [];
                foreach ($this->appointmentsetting as $key => $hours) {
                    foreach($hours as $hour => $value){
                        $applystate = $idstate;
                        $applyvalue = $value;
                        if($this->disabledhours){
                            if(isset($this->disabledhours[$key])){
                                if(isset($this->disabledhours[$key][$hour])){
                                    $applystate = $disabledstate;
                                    $applyvalue = 0;
                                }
                            }
                        }
                        $aponint = Appointmentservicesetting::findOne(['IdDay'=>$key,'IdHour'=>$hour,'IdServiceCentre'=>  $this->Id]);
                        if($aponint == NULL){
                            $aponint = new Appointmentservicesetting();
                            $aponint->scenario = Appointmentservicesetting::SCENARIO_BATCH;
                            $aponint->IdServiceCentre = $this->Id;
                            $aponint->IdDay = $key;
                            $aponint->IdHour = $hour;
                        } 
                        $aponint->IdState = $applystate;
                        $aponint->Quantity = $applyvalue;
                        #$data[]=$aponint;
                        if(!$aponint->save()){
                            $message = Yii::$app->customFunctions->getErrors($aponint->errors);
                            throw new Exception($message, 92000);
                        }
                    }
                }
                $transaction->commit();
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        } 
    }
    
    public function getSuggestions(){
        try {
            if($this->Id){
                $setting = Settingdetail::find()
                        ->joinWith('setting b')
                        ->where(['b.KeyWord'=> StringHelper::basename(self::class),'b.Code'=> self::SUGGESTION_CODE])
                        ->andWhere(Settingdetail::tableName().".Code LIKE '$this->Id-%'")
                        ->asArray()
                        ->all();
                foreach ($setting as $set){
                    $s = self::findOne(['Id'=>$set["Value"]]);
                    if(!empty($s)){
                        $this->suggest[$s->Id] = $s->Name;
                    }
                }
                $this->_setSuggestedList();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _setSuggestedList(){
        try {
            $suggest = [];
            foreach ($this->suggest as $key => $val){
                $js = new JsExpression("setService($key);");
                $link = Html::a($val, "javascript:$js");#"<a href='$js'>$val</a>";
                array_push($suggest, $link);
            }
            if(count($suggest) > 0){
                $this->suggestList =  Html::label('Duicentros sugeridos:');
                $this->suggestList .= Html::ul($suggest, ['encode'=>FALSE]);
            } else {
                $this->suggestList = NULL;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    

}
