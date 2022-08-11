<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
#use common\models\CustomActiveRecord;
use nepstor\validators\DateTimeCompareValidator;
use Exception;

use common\models\Citizen;
use common\models\State;
use common\models\User;
use common\models\Type;
use common\models\Servicecentre;
use backend\models\Settingdetail;
use backend\models\Appointmentservicesetting;
use DateTime;

/**
 * This is the model class for table "appointment".
 *
 * @property integer $Id
 * @property integer $IdCitizen
 * @property string $AppointmentDate
 * @property string $AppointmentHour
 * @property string $Code
 * @property string $ShortCode
 * @property integer $IdState
 * @property integer $IdType
 * @property integer $IdServiceCentre
 * @property string $CreationDate
 * @property string $CancelationDate
 * @property string $RegistrationMethod
 * @property integer $IdUser
 * @property integer $IdCancelUser
 *
 * @property State $state
 * @property State $type
 * @property Citizen $citizen
 * @property User $user
 * @property User $userCancel
 * @property Servicecentre $serviceCentre
 */
class Appointment extends \yii\db\ActiveRecord
{
    use traits\AppointmentValidationtrait;

    public $view;
    public $create;
    public $update;
    public $delete;
    public $cancel;
    public $inactive = false;
    public $reschedule;
    public $citizenName;
    public $hourDate;
    public $sendmail;
    public $sendremindermail;
    public $enabledSunday = false;
    
    public $finishDate;
    public $_finishYear;
    public $_finishMonth;
    public $_finishDay;
    public $_finishHour;
    public $_finishMinute = 0;
    public $rawHour = null;

    private $_date;
    private $_dateTime;
    private $_hour;
    
    private $_count = 0;
    private $_correlative = 0;
    private $_max_request = 2;
    private $_weekday;
    public $wDay;
    
    public $RegistrationMethodName = 'No Definido';
    public $response_format = 'ARRAY';
    private $date_format = 'd-m-Y';
    private $dbDateFormat = '%d-%m-%Y';
    private $time_format = 'H:i';

    private $allowPartial = false;
    private $valuePartialHours = null;

    const RELEASE_SPACE = 'RELEASE';
    const ALLOWPARTIALHOUR_CODE = 'ALLOWPARTIALHOUR';
    const PARTIALHOURVALUE_CODE = 'PARTIALHOURVALUE';
    const ALLOWPARTIALHOUR = 0;
    const ALLOWPARTIALHOUR_ENABLED = 1;
    const ALLOWPARTIALHOUR_DISABLED = 0;
    const PARTIALHOURVALUE_DEFAULT = 30;
    const PARTIALHOURSEGMENT_DEFAULT = 1;
    const DEFAULT_LENGTH_CODE = 5;
    const ENABLEDSUNDAY_CODE = 'ENABLEDSUNDAY';
    const ENABLEDSUNDAY_DEFAULT_VALUE = false;

    const ACTIVE_STATUS = 'ACT';
    const INACTIVE_STATUS = 'INA';
    const UNATTENDED_STATUS = 'NAT';
    const CANCELED_STATUS = 'CAN';
    const ATTENDED_STATUS = 'ATTD';
    
    const TRANSACTION_KEYWORD = 'DocumentTransaction';
    const REGISTRATION_METHOD_KEYWORD = 'RegistrationMethod';
    
    const RESPONSE_FORMAT_GRID = 'GRID';
    const RESPONSE_FORMAT_ARRAY = 'ARRAY';
    
    const SETTING_CODE = 'SETTING';
    const SHORTCODE_CODE = 'SHCODE';
    const APPOINTMENT_HOUR_QTY = 'QTY';
    const APPOINTMENT_BEFORE_DAY_QTY = 'QTYDA';
    const APPOINTMENT_DAY_MAX_QTY = 'QTYMAX';
    const APPOINTMENT_DEFAULT_QTY = 4;
    const RESCHEDULE_CODE = 'RESCH';
    const REMINDER_CODE = 'RMND';

    private $unvalidatedScenarios = ['cancel','inactive'];
    private $_operativeStates = [];
    const SCENARIO_CANCEL = 'cancel';
    const SCENARIO_INACTIVE = 'inactive';
    const SCENARIO_CONSOLE = 'console';
    
    public $message = "";

    private $dayname = [
        '1'=>'Lunes',
        '2'=>'Martes',
        '3'=>'Miércoes',
        '4'=>'Jueves',
        '5'=>'Viernes',
        '6'=>'Sábado',
        '7'=>'Domingo',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment';
    }
    
    /*
    public function behaviors() {
        
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['CreationDate'],
            ],
            'value'=>new Expression('NOW()'),
        ];
        return $behaviors;
    }
    */
   public function __construct($config = array()) {
       $this->_operativeStates = [
           self::ACTIVE_STATUS, self::ATTENDED_STATUS, self::UNATTENDED_STATUS
       ];
       return parent::__construct($config);
   }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CANCEL] = ['Id','IdCitizen','IdState','AppointmentHour','CancelationDate','IdCancelUser'];
        $scenarios[self::SCENARIO_INACTIVE] = ['Id','IdCitizen','IdState','AppointmentHour','CancelationDate','IdCancelUser'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $date = date_create(date($this->date_format));
        $hour = date_create(date($this->time_format));
        $da = $this->getDayBefore();
        $wDay = (int) $date->format('w');
        $hDay = (int) $hour->format('H');
        $this->setSundaySetting();
        $da = ($this->enabledSunday ? $da : (($wDay == 5 && $hDay >= 16) ? ($da+2) : $da ));
        date_add($date, date_interval_create_from_date_string("$da day"));
        $this->finishDate = date_format($date, $this->date_format);
        $this->_finishYear = date_format($date, 'Y');
        $this->_finishMonth = date_format($date, 'm');
        $this->_finishDay = date_format($date, 'd');
        $this->_finishHour = $this->getSettingHour();
        return [
            [['IdCitizen', 'IdState', 'IdServiceCentre','IdType'], 'required','message'=>'{attribute} no puede quedar vacío'],
            [['IdCitizen', 'IdState', 'IdServiceCentre','IdType','IdUser','IdCancelUser'], 'integer'],
            [['AppointmentHour','CreationDate','CancelationDate'], 'safe'],
            [['AppointmentDate'], 'date'],
            [['Code'], 'string', 'max' => 50],
            [['ShortCode'], 'string', 'max' => 8],
            [['RegistrationMethod'], 'string', 'max' => 50],
            [['Code'], 'unique','message'=>'Código {value} ya existe'],
            ['AppointmentDate', 'dateValidation'],
            [['AppointmentDate','AppointmentHour'], 'required','on'=>'default','message'=>'{attribute} no puede quedar vacío'],
            [['AppointmentDate'],  DateTimeCompareValidator::class,'compareValue'=> Yii::$app->formatter->asDate($date, 'php:'.$this->date_format)
                ,'operator'=>'>=','format'=> $this->date_format,'jsFormat'=>'DD-MM-YYYY'
                ,'message'=>'{attribute} debe ser mayor o igual que '.$this->finishDate, 'on' => 'create'],
            [['AppointmentDate'],  DateTimeCompareValidator::class,'compareValue'=> Yii::$app->formatter->asDate($date, 'php:'.$this->date_format)
                ,'operator'=>'>=','format'=> $this->date_format,'jsFormat'=>'DD-MM-YYYY'
                ,'message'=>'{attribute} debe ser mayor o igual que '.$this->finishDate,'on' => 'default'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['IdCitizen'], 'exist', 'skipOnError' => true, 'targetClass' => Citizen::class, 'targetAttribute' => ['IdCitizen' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdCancelUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdCancelUser' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdServiceCentre'],function ($attribute, $params, $validator) {
                $centre = Servicecentre::findOne(['Id'=> $params]);
                if($centre){
                    if($centre->state == Servicecentre::STATE_INACTIVE){
                        $this->addError('IdServiceCentre', 'El Duicentro se encuentra inhabilitado para registrar citas');
                    }
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdCitizen' => 'Ciudadano',
            'citizenName' => 'Nombre del Ciudadano',
            'AppointmentDate' => 'Fecha',
            'AppointmentHour' => 'Hora',
            'IdState' => 'Estado',
            'IdType' => 'Tipo de Trámite',
            'IdServiceCentre' => 'Duicentro',
            'Code'=>'Código',
            'ShortCode'=>'Código',
            'RegistrationMethod'=>'Registro',
            'RegistrationMethodName'=>'Registro',
            'IdUser'=>'Usuario',
            'CreationDate' => 'Fecha Registro',
            'CancelationDate' => 'Fecha Cancelación',
            'IdCancelUser' => 'Usuario Cancelación',
        ];
    }

    /*Maintainance Methods*/
    public function beforeValidate() {
        $this->_validateScenario();
        return parent::beforeValidate();
    }
    
    public function afterValidate() {
        return parent::afterValidate();
    }
    
    private function _validateScenario(){
        try {

        } catch (Exception $exc) {
            throw $exc;
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
            $droptions = State::findAll(['KeyWord'=> StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getOperativeStates(){
        try {
            $droptions = State::findAll(['KeyWord'=> StringHelper::basename(self::class), 'Code' => $this->_operativeStates]);
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
            $droptions = Type::find()
                    ->joinWith('state b')
                    ->where(['type.KeyWord'=> self::TRANSACTION_KEYWORD ,'b.Code'=>  Type::STATUS_ACTIVE])
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getRegistrationMethods(){
        $types = Type::find()
                ->joinWith('state b', false)
                ->where([
                    'b.Code' => Type::STATUS_ACTIVE,
                    Type::tableName().'.KeyWord'=> self::REGISTRATION_METHOD_KEYWORD
                ])->select([Type::tableName().'.Code', Type::tableName().'.Name'])->all();
        return ArrayHelper::map($types, 'Code', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCitizen()
    {
        return $this->hasOne(Citizen::class, ['Id' => 'IdCitizen']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCancel()
    {
        return $this->hasOne(User::class, ['Id' => 'IdCancelUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }
    
    public function getServiceCentres(){
        try {
            $droptions = Servicecentre::find()
                    ->joinWith('state b')
                    ->joinWith('type c')
                    ->where(['b.Code'=> Servicecentre::STATE_ACTIVE, 'c.Code'=> Servicecentre::TYPE_DUISITE])
                    ->orderBy([Servicecentre::tableName().'.Name'=> SORT_ASC])
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function afterFind() {
        if(\Yii::$app->id == "app-client"){
            $this->cancel = TRUE;
            $this->reschedule = TRUE;
            $values = Settingdetail::find()->joinWith('setting b', TRUE)
                        ->where([
                            'b.KeyWord'=> StringHelper::basename(self::class)
                            ,'b.Code' => self::SETTING_CODE
                            , Settingdetail::tableName().'.Code'=> self::RESCHEDULE_CODE])
                        ->one();
            $this->reschedule = $values != NULL ? ((int)$values->Value == 1):FALSE;
        } elseif(in_array(\Yii::$app->id, ['app-backend','app-frontend']) ){

            $this->view = \Yii::$app->user->can('appointmentView');
            $this->create = \Yii::$app->user->can('appointmentCreate');
            $this->update = \Yii::$app->user->can('appointmentUpdate');
            $this->delete = \Yii::$app->user->can('appointmentDelete');
            $this->cancel = \Yii::$app->user->can('appointmentCancel');
            $this->inactive = \Yii::$app->user->can('appointmentInactive');
            $this->reschedule = \Yii::$app->user->can('appointmentReschedule');
            $this->sendremindermail = \Yii::$app->user->can('appointmentSendremindermail');
        } else {}
        
        $this->cancel = $this->cancel && ($this->IdState ? ($this->state->Code == self::ACTIVE_STATUS ?  true : false): false);
        $this->inactive = $this->inactive && ($this->IdState ? ($this->state->Code != self::INACTIVE_STATUS ? true : false): false);
        $this->sendmail = ($this->IdState ? ($this->state->Code == self::ACTIVE_STATUS ? true : false): false);
        $this->sendremindermail = $this->sendremindermail && ($this->IdState ? ($this->state->Code == self::ACTIVE_STATUS ?  true : false): false);
        $this->reschedule = $this->reschedule && ($this->IdState ? ($this->state->Code ==  self::ACTIVE_STATUS ?  true : false): false);
        $this->rawHour = $this->AppointmentHour;
        $this->AppointmentDate = Yii::$app->formatter->asDate($this->AppointmentDate, 'php:d-m-Y');
        $this->AppointmentHour = Yii::$app->formatter->asTime($this->AppointmentHour, 'php:h:i a');
        $this->_dateTime = DateTime::createFromFormat('d-m-Y H:i:s',$this->AppointmentDate.' '.$this->rawHour);
        $this->citizenName = $this->IdCitizen ? $this->citizen->CompleteName:"";
        $this->CreationDate = !empty($this->CreationDate) ? Yii::$app->formatter->asDatetime($this->CreationDate,'php:d-m-Y H:i:s'):$this->CreationDate;
        $this->CancelationDate = !empty($this->CancelationDate) ? Yii::$app->formatter->asDatetime($this->CancelationDate,'php:d-m-Y H:i:s'):$this->CancelationDate;
        if($this->RegistrationMethod){
            $type = Type::findOne(['KeyWord'=>'RegistrationMethod','Code'=> $this->RegistrationMethod]);
            if($type){
                $this->RegistrationMethodName = $type->Name;
            }
        }
        return parent::afterFind();
    }

    public function beforeSave($insert) {
        try {
           if($this->isNewRecord){
                $this->CreationDate = (!empty($this->CreationDate) ? (Yii::$app->formatter->asDate($this->CreationDate,'php:Y') == '0000' ? date('Y-m-d H:i:s'):Yii::$app->formatter->asDatetime($this->CreationDate,'php:Y-m-d h:i:s')): date('Y-m-d H:i:s'));
            } else {
                $this->CreationDate = !empty($this->CreationDate) ? Yii::$app->formatter->asDate($this->CreationDate,'php:Y-m-d H:i:s'):$this->CreationDate;
                $this->CancelationDate = !empty($this->CancelationDate) ? Yii::$app->formatter->asDate($this->CancelationDate,'php:Y-m-d H:i:s'):$this->CancelationDate;
            }
            $this->_validateScenario();
            if(parent::beforeSave($insert)){
                $this->_dateTime = DateTime::createFromFormat('d-m-Y H:i:s',$this->AppointmentDate.' '.$this->rawHour);
                $this->_loadDefaultData();
                $this->AppointmentDate = Yii::$app->formatter->asDate($this->AppointmentDate, 'php:Y-m-d');
                $this->AppointmentHour = $this->rawHour;
                if(in_array(StringHelper::basename(Yii::getAlias('@app')), ['backend','frontend'])){
                    $this->IdUser = Yii::$app->user->getIdentity()->getId();
                }
                return ((in_array($this->getScenario(), $this->unvalidatedScenarios)) ? TRUE:($this->validateData() && $this->validateCenter()));
            } else {
                return false;
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    private function _loadDefaultData(){
        try {
            $this->_setCriteria();
            if($this->isNewRecord){
                $this->_correlative = $this->getCorrelative();
                $state = State::findOne(['KeyWord'=>StringHelper::basename(self::class),'Code'=>  self::ACTIVE_STATUS]);
                $this->IdState = $state->Id;
                $this->getCode();
             } elseif($this->ShortCode == NULL){
                 $this->_generateShortCode();
             }
             $this->RegistrationMethod = \Yii::$app->id;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getDateFormat(){
        return $this->date_format;
    }
    
    public function getDbDateFormat(){
        return $this->dbDateFormat;
    }
    
    public function setDateParam($date = NULL){
        $this->_date = $date;
    }

    public function getAppointmentDate(){
        try {
            #$dia = $this->dayname[(\Yii::$app->formatter->asDate($this->AppointmentDate, 'e') - 1)];
            return \Yii::$app->formatter->asDate($this->AppointmentDate, 'php:d/m/Y');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function getAppointmentHour(){
        try {
            return $this->_dateTime->format('h:i a');
            #return \Yii::$app->formatter->asTime(, 'php:h:i a');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function validateData() {
        try {
            $criteria = ['IdState'=>  State::findOne(['KeyWord'=>StringHelper::basename(self::class),'Code'=>  self::ACTIVE_STATUS])->Id,'IdCitizen'=>  $this->IdCitizen];
            if($this->Id != NULL){
                $model = self::findByCondition($criteria)->andWhere('Id != :id',[':id'=>  $this->Id])->count('*');

            } else {
                $model = self::findByCondition($criteria)->count('*');
            }
            if($model > 0){
                $this->addError('AppointmentDate','Ciudadano ya posee una cita agendada');
                return FALSE;
            } else {return TRUE;}
        } catch (Exception $exc) {
            throw $exc;
        }
            
    }
    
    private function validateCenter() {
        try{
            $criteria = ['IdState'=>  State::findOne(['KeyWord'=>StringHelper::basename(self::class),'Code'=>  self::ACTIVE_STATUS])->Id,
                'IdServiceCentre'=>  $this->IdServiceCentre,
            ];
            
            $this->_setCriteria();
            
            $this->getCountAppointment($criteria);
            $this->setMaxRequest();
            
            if($this->_count >= $this->_max_request){
                $this->addError('AppointmentDate','La Cantidad de citas de Duicentro para esta hora se ha completado. Seleccione otro horario');
                return FALSE;
            } else {return TRUE;}
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    private function _setCriteria(){
        try {
            $this->_hour = Yii::$app->getFormatter()->asDatetime($this->AppointmentDate.' '.$this->rawHour, 'php:H');
            $this->_weekday = ((int) Yii::$app->getFormatter()->asDate($this->AppointmentDate, 'php:w'))+1;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function cancel(){
        try {
            $state = State::findOne(['KeyWord'=>  StringHelper::basename(self::class),'Code'=>  self::CANCELED_STATUS]);
            $this->IdState = $state->Id;
            $this->CancelationDate = date('d-m-Y H:i:s');
            if(!in_array(StringHelper::basename(Yii::getAlias('@app')), ['client','console'])){
                $this->IdCancelUser = Yii::$app->getUser()->getIdentity()->Id;
            }
            if(!$this->save()){
                $message = Yii::$app->customFunctions->getErrors($this->errors);
                throw new Exception($message, 92000);
            } else {
                return true;
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function inactive(){
        try {
            $state = State::findOne(['KeyWord'=>  StringHelper::basename(self::class),'Code'=>  self::INACTIVE_STATUS]);
            $this->IdState = $state->Id;
            $this->CancelationDate = date('d-m-Y H:i:s');
            if(!in_array(StringHelper::basename(Yii::getAlias('@app')), ['client','console'])){
                $this->IdCancelUser = Yii::$app->getUser()->getIdentity()->Id;
            }
            if(!$this->save()){
                $message = Yii::$app->customFunctions->getErrors($this->errors);
                throw new Exception($message, 92000);
            } else {
                return true;
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    private function setMaxRequest(){
        try {
            $validate = $this->_getServicecentresetting();
            if($validate){
                $values = Settingdetail::find()->joinWith('setting b', TRUE)
                        ->where([
                            'b.KeyWord'=> StringHelper::basename(self::class), 
                            'b.Code'=> self::SETTING_CODE, 
                            Settingsdetail::tableName().'.Code'=> self::APPOINTMENT_HOUR_QTY
                        ])
                        ->one();
                $this->_max_request = $values != NULL ? $values->Value:$this->_max_request;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    private function _getServicecentresetting(){
        try {
            $validate = TRUE;
            $setting = Appointmentservicesetting::findOne(['IdServiceCentre'=>  $this->IdServiceCentre,'IdDay'=>  $this->_weekday,'IdHour'=>  $this->_hour]);
            $this->_max_request = self::APPOINTMENT_DEFAULT_QTY;
            if($setting != NULL){
                if($setting->state->Code == Appointmentservicesetting::DEFAULT_STATE){
                    $this->_max_request = $setting->Quantity;
                    $validate = false;
                } 
            }
            return $validate;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function getCountAppointment($criteria = NULL){
        try {
            if(!$this->isNewRecord){
                $this->_count = self::findByCondition($criteria)
                        ->andWhere('Id != :id',[':id'=>  $this->Id])
                        ->andWhere("date_format(AppointmentDate,'%Y-%m-%d') = :fecha",[':fecha'=> $this->_date])
                        ->andWhere("date_format(AppointmentHour,'%H') = :hora",[':hora'=> $this->_hour])
                        ->count('*');
                
            } else {
                $this->_count = self::findByCondition($criteria)
                        ->andWhere("date_format(AppointmentDate,'%Y-%m-%d') = :fecha",[':fecha'=> $this->_date])
                        ->andWhere("date_format(AppointmentHour,'%H') = :hora",[':hora'=> $this->_hour])
                        ->count('*');
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function getCorrelative(){
        try {
            $_date  = Yii::$app->getFormatter()->asDate($this->AppointmentDate,'php:Y-m-d');
            $count = self::find()->where(['IdServiceCentre'=> $this->IdServiceCentre])
                    ->andWhere("AppointmentDate = date_format(:fecha,'%Y-%m-%d')",[':fecha'=> $_date])
                    ->count('*');
            return $count;
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }

    private function getCode(){
        try {
            
            $this->Code = NULL;
            $type = Type::findOne(['Id'=>  $this->IdType]);
            $idService = (int) $this->IdServiceCentre ? ($this->serviceCentre->MBCode ? $this->serviceCentre->MBCode:$this->IdServiceCentre):0;
            $servicecentre = str_pad($idService, 3, '0', STR_PAD_LEFT);
            $fdate = date_format(new \DateTime($this->AppointmentDate),'Ymd');
            $fhour = \Yii::$app->formatter->asTime($this->AppointmentHour, 'hh');
            $fsecond = date('is');
            $corr = str_pad(((int) $this->_correlative + 1), 3,'0',STR_PAD_LEFT);
            $this->Code = $type->Code.'-'.$servicecentre.'-'.$fdate.$fhour.$fsecond.$corr;
            $this->_generateShortCode();

        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    private function _generateShortCode(){
        try {
            $length = $this->_getLengthCode();
            $corr = $this->_correlative + 1;
            $right = $length - 2;
            $this->ShortCode = str_pad(($this->serviceCentre->MBCode), 2,'0',STR_PAD_LEFT)."".str_pad(($corr), $right,'0',STR_PAD_LEFT);
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    private function _getLengthCode(){
        try {
            $lenght = self::DEFAULT_LENGTH_CODE;
            $value = Settingdetail::find()
                    ->joinWith('setting b',true)
                    ->where([
                        'b.KeyWord'=> StringHelper::basename(self::class) 
                        , 'b.Code' => self::SETTING_CODE
                        , Settingdetail::tableName().'.Code'=> self::SETTING_CODE
                    ])->one();
            if($value != NULL){
                $lenght = $value->Value;
            }
            return $lenght;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}