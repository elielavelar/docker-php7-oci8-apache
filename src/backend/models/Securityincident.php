<?php

namespace backend\models;

use Yii;
use common\models\User;
use common\models\State;
use common\models\Type;
use common\models\Servicecentre;
use backend\models\Securityincidentdetails;
use backend\models\Attachments;
use backend\models\Settingdetail;

use backend\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\db\Query;

/**
 * This is the model class for table "securityincident".
 *
 * @property int $Id
 * @property string $Ticket
 * @property string $TicketDate
 * @property string $IncidentDate
 * @property string $InterruptDate
 * @property string $SolutionDate
 * @property string $Title
 * @property int $IdServiceCentre
 * @property int $IdIncident
 * @property int $IdReportUser
 * @property int $IdType
 * @property int $IdState
 * @property int $IdLevelType
 * @property int $IdPriorityType
 * @property int $IdInterruptType
 * @property int $IdUser
 * @property int $IdCreateUser
 * @property int $IdCategoryType
 * @property string $Description
 *
 * @property Incidentcategory $categoryType
 * @property User $createUser
 * @property Incident $incident
 * @property Type $interruptType
 * @property Type $levelType
 * @property Type $priorityType
 * @property User $reportUser
 * @property Servicecentre $serviceCentre
 * @property State $state
 * @property Type $type
 * @property User $user
 * @property Securityincidentdetails[] $securityincidentdetails
 */
class Securityincident extends CustomActiveRecord
{
    
    const STATE_REGISTRED = 'REG';
    const STATE_INPROCESS = 'PRC';
    const STATE_SOLVED = 'SLV';
    const STATE_CLOSED = 'CLS';
    const STATE_CANCELED = 'CNC';
    
    const INTERRUPT_TYPE_WITHOUT = 'SINT';
    const INTERRUPT_TYPE_LOCAL_PARTIAL = 'LOCP';
    const INTERRUPT_TYPE_LOCAL_TOTAL = 'LOCT';
    const INTERRUPT_TYPE_GLOBAL_PARTIAL = 'GLOBP';
    const INTERRUPT_TYPE_GLOBAL_TOTAL = 'GLOBT';
    
    const PRIORITY_LOW = 'LOW';
    const PRIORITY_MEDIUM = 'MED';
    const PRIORITY_HIGH = 'HIGH';
    
    const LEVEL_WITHOUT_RISK = 'SRSK';
    const LEVEL_LOW = 'LOW';
    const LEVEL_MED = 'MED';
    const LEVEL_HIGH = 'HIGH';
    
    const DEFAULT_USER_CODE = 'DFLTUSR';
    
    private $_saveDetail = FALSE;
    
    public $attachments = [];
    public $solution_time = 0;
    public $HelpDeskTicket = null;
    public $Year = null;
    public $_idServiceCentre = null;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'securityincident';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Title', 'IdServiceCentre', 'IdReportUser', 'IdType', 'IdState', 'IdLevelType', 'IdPriorityType', 'IdInterruptType', 'IdCreateUser', 'IdCategoryType','Description'], 'required'],
            [['Ticket', 'IdServiceCentre', 'IdIncident', 'IdReportUser', 'IdType', 'IdState', 'IdLevelType', 'IdPriorityType', 'IdInterruptType', 'IdUser', 'IdCreateUser', 'IdCategoryType'], 'integer'],
            [['TicketDate', 'IncidentDate', 'InterruptDate', 'SolutionDate'], 'safe'],
            [['Description'], 'string'],
            [['Title'], 'string', 'max' => 250],
            [['IdCategoryType'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentcategory::className(), 'targetAttribute' => ['IdCategoryType' => 'Id']],
            [['IdCreateUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdCreateUser' => 'id']],
            [['IdIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Incident::className(), 'targetAttribute' => ['IdIncident' => 'Id']],
            [['IdInterruptType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdInterruptType' => 'Id']],
            [['IdLevelType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdLevelType' => 'Id']],
            [['IdPriorityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdPriorityType' => 'Id']],
            [['IdReportUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdReportUser' => 'id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::className(), 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdType' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Ticket' => 'Ticket',
            'TicketDate' => 'Fecha Reporte',
            'IncidentDate' => 'Fecha Incidente',
            'InterruptDate' => 'Fecha Interrupción',
            'SolutionDate' => 'Fecha Solución',
            'Title' => 'Título',
            'IdServiceCentre' => 'Departamento',
            'IdIncident' => 'Ticket Help Desk',
            'IdReportUser' => 'Usuario Reporta',
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'IdLevelType' => 'Nivel',
            'IdPriorityType' => 'Prioridad',
            'IdInterruptType' => 'Tipo Interrupción',
            'IdUser' => 'Usuario',
            'IdCreateUser' => 'Usuario Creación',
            'IdCategoryType' => 'Tipo Incidente',
            'Description' => 'Descripción',
            'solution_time' => 'Tiempo Solución',
            'HelpDeskTicket' => 'Ticket Help Desk',
            'Year' => 'Año',
            '_idServiceCentre' => 'Departamento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryType()
    {
        return $this->hasOne(Incidentcategory::className(), ['Id' => 'IdCategoryType']);
    }
    
    public function getCategoryTypes(){
        $droptions = Incidentcategory::find()
                ->joinWith('state b')
                ->where(['b.Code' => Incidentcategory::STATUS_ACTIVE])
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'IdCreateUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncident()
    {
        return $this->hasOne(Incident::className(), ['Id' => 'IdIncident']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterruptType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdInterruptType']);
    }

    public function getInterruptTypes(){
        $droptions = Type::find()
                ->joinWith('state b')
                ->where(['b.Code' => Type::STATUS_ACTIVE,  'type.KeyWord' => StringHelper::basename(Incident::class).'Interrupt'])
                ->orderBy(['type.Value' => SORT_ASC])
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevelType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdLevelType']);
    }

    public function getLevelTypes(){
        $droptions = Type::find()
                ->joinWith('state b')
                ->where(['b.Code' => Type::STATUS_ACTIVE,  'type.KeyWord' => StringHelper::basename(self::class).'Level'])
                ->orderBy(['type.Value' => SORT_ASC])
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriorityType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdPriorityType']);
    }

    public function getPriorityTypes(){
        $droptions = Type::find()
                ->joinWith('state b')
                ->where(['b.Code' => Type::STATUS_ACTIVE,  'type.KeyWord' => StringHelper::basename(Incident::class).'Priority'])
                ->orderBy(['type.Value' => SORT_ASC])
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportUser()
    {
        return $this->hasOne(User::className(), ['id' => 'IdReportUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::className(), ['Id' => 'IdServiceCentre']);
    }
    
    public function getServiceCentres(){
        $droptions = Servicecentre::find()
                ->joinWith('state b')
                ->where(['b.Code' => Servicecentre::STATE_ACTIVE])
                ->andWhere('IdParent IS NOT NULL')
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $droptions = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdType']);
    }
    
     public function getTypes(){
        $droptions = Type::find()
                ->joinWith('state b')
                ->where(['b.Code' => Type::STATUS_ACTIVE,  'type.KeyWord' => StringHelper::basename(self::class)])
                ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'IdUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecurityincidentdetails()
    {
        return $this->hasMany(Securityincidentdetails::className(), ['IdSecurityIncident' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        #return $this->hasMany(Attachments::className(), ['KeyWord' => StringHelper::basename(self::class), 'AttributeName' => 'Id', 'AttributeValue' => $this->Id]);
        return $this->attachments = Attachments::find()->where(['KeyWord' => StringHelper::basename(self::class), 'AttributeName' => 'Id', 'AttributeValue' => $this->Id])->all();
    }
    
    
    private function _generateNumTicket (){
        try {
            $date = date_create_from_format('Y-m-d H:i:s', $this->TicketDate);#date('ymd');
            $service = Servicecentre::find()->where(['Id'=> $this->IdServiceCentre])->one();
            $id = (int) $this->_getLastId() + 1;
            $this->Ticket = $date->format('ymd').str_pad($service->MBCode,3,'0', STR_PAD_LEFT).str_pad($id,4,'0',STR_PAD_LEFT);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getLastId(){
        try {
            return self::find()->max('Id');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        $this->IncidentDate = Yii::$app->getFormatter()->asDate($this->IncidentDate, 'php:Y-m-d H:i:s');
        $this->TicketDate = Yii::$app->getFormatter()->asDate($this->TicketDate, 'php:Y-m-d H:i:s');
        $this->InterruptDate = $this->InterruptDate ? Yii::$app->getFormatter()->asDate($this->InterruptDate,'php:Y-m-d H:i:s'):$this->InterruptDate;
        $this->SolutionDate = $this->SolutionDate ? Yii::$app->getFormatter()->asDate($this->SolutionDate,'php:Y-m-d H:i:s'):$this->SolutionDate;

        if($this->isNewRecord){
            $this->_generateNumTicket();
            $this->IdState = State::findOne(['KeyWord'=> StringHelper::basename(self::class),'Code'=> self::STATE_REGISTRED])->Id;
            $this->_assignDefaultUser();
            $this->_saveDetail = TRUE;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            if($this->_saveDetail){
                $this->refresh();
                $this->_createDefaultDetail();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterFind() {
        try {
            $this->IncidentDate = $this->IncidentDate ? Yii::$app->formatter->asDate($this->IncidentDate,'php:d-m-Y H:i:s'):$this->IncidentDate;
            $this->InterruptDate = $this->InterruptDate ? Yii::$app->formatter->asDate($this->InterruptDate,'php:d-m-Y H:i:s'):$this->InterruptDate;
            $this->TicketDate = $this->TicketDate ? Yii::$app->formatter->asDate($this->TicketDate,'php:d-m-Y H:i:s'):$this->TicketDate;
            $this->SolutionDate = $this->SolutionDate ? Yii::$app->formatter->asDate($this->SolutionDate,'php:d-m-Y H:i:s'):$this->SolutionDate;
            $this->HelpDeskTicket = $this->IdIncident ? $this->incident->Ticket : null;
            $this->_calculateTime();
            $this->getAttachments();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function beforeDelete() {
        try {
            $this->_deleteAttachments();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeDelete();
    }
    
    public function afterDelete() {
        
        return parent::afterDelete();
    }
    
    private function _deleteAttachments(){
        try {
            foreach ($this->attachments as $att){
                if(!$att->delete()){
                    $message = Yii::$app->customFunctions->getErrors($att->errors);
                    throw new Exception($message, 93099);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _assignDefaultUser(){
        try {
            $setting = Settingdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord' => StringHelper::basename(self::class)
                            ,'b.Code' => self::DEFAULT_USER_CODE
                            ,'settingsdetail.Code' => self::DEFAULT_USER_CODE
                        ])
                    ->one();
            if(!empty($setting)){
                $user = User::findOne(['Username' => $setting->Value]);
                $_user = !empty($user) ? $user->Id : Yii::$app->user->getIdentity()->getId();
            } else {
                $_user = Yii::$app->user->getIdentity()->getId();
            }
            $this->IdUser = $_user;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _createDefaultDetail(){
        try {
            $detail = new Securityincidentdetails();
            $detail->IdSecurityIncident = $this->Id;
            $detail->Title = 'Asignación Automática de Incidencia de Seguridad';
            $detail->IdUser = $this->IdUser;
            $detail->IdAssignedUser = $this->IdUser;
            $detail->IdActivityType = Type::findOne(['KeyWord' => StringHelper::basename(Securityincidentdetails::class).'Activity','Code' => Securityincidentdetails::ACTIVITY_ASSIGNMENT])->Id;
            $detail->IdIncidentState = $this->IdState;
            if(!$detail->save()){
                $errors = $detail->getErrors();
                $message = Yii::$app->customFunctions->getErrors($errors);
                throw new Exception($message, 94000);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _calculateTime(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getYearList(){
        $yearList = [];
        try {
            $query= new Query();
            $secincidents = $query->select(["date_format(TicketDate, '%Y') as YearInc"])
                    ->from('securityincident')
                    ->groupBy(["date_format(TicketDate, '%Y')"])
                    ->all();
            $currenYear = date('Y');
            $existCurrentYear = false;
            foreach ($secincidents as $key => $y ){
                $yearList[] = ['Year' => $y['YearInc']];
                $existCurrentYear = $existCurrentYear ? $existCurrentYear : $currenYear == $y['YearInc'];
            }
            if(!$existCurrentYear){
                $yearList[] = ['Year' => $currenYear];
            }
            return ArrayHelper::map($yearList, 'Year', 'Year');
            #return $yearList;
        } catch (Exception $ex) {
            throw $ex;
        } 
    }
}
