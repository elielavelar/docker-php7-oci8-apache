<?php

namespace backend\models;

use common\models\Attachment;
use common\models\Resource;
use Yii;
use common\models\Type;
use common\models\State;
use common\models\User;
use common\models\Servicecentre;
use backend\models\traits\Incidenttrait;

use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;

/**
 * This is the model class for table "incident".
 *
 * @property int $Id
 * @property int $IdTitle
 * @property string $Ticket
 * @property string $IncidentDate
 * @property string $TicketDate
 * @property string $InterruptDate
 * @property string $SolutionDate
 * @property int $IdServiceCentre
 * @property int $IdReportUser
 * @property int $IdCategoryType
 * @property int $IdSubCategoryType
 * @property int $IdInterruptType
 * @property int $IdPriorityType
 * @property int $IdRevisionType
 * @property int $IdState
 * @property string $Commentaries
 * @property int $IdUser
 * @property int $IdCreateUser
 * @property int $IdParentIncident
 * @property int $IdIncidentRequest
 * @property int $IdResource
 *
 * @property Incidentcategory $categoryType
 * @property Incidentcategory $subCategoryType
 * @property Type $interruptType
 * @property Type $priorityType
 * @property User $reportUser
 * @property Type $revisionType
 * @property Servicecentre $serviceCentre
 * @property State $state
 * @property User $user
 * @property User $createUser
 * @property Incident $parentIncident
 * @property Resource $resource
 * @property Incidenttitle $title
 * @property Incidentrequest $incidentRequest
 * @property Incidentdetail[] $incidentdetails
 * @property Incidentresource[] $incidentresources
 * @property Attachment[] $attachments
 */
class Incident extends \yii\db\ActiveRecord
{
    use Incidenttrait;

    const SCENARIO_CREATE = 'create';
    const STATE_OPENED = 'OPN';
    const STATE_ASSIGNED = 'ASG';
    const STATE_INPROCESS = 'PRC';
    const STATE_SOLVED = 'RSV';
    const STATE_CLOSED = 'CLS';
    const STATE_CANCELED = 'ANU';
    
    const INTERRUPT_TYPE_WITHOUT = 'SINT';
    const INTERRUPT_TYPE_LOCAL_PARTIAL = 'LOCP';
    const INTERRUPT_TYPE_LOCAL_TOTAL = 'LOCT';
    const INTERRUPT_TYPE_GLOBAL_PARTIAL = 'GLOBP';
    const INTERRUPT_TYPE_GLOBAL_TOTAL = 'GLOBT';
    
    const PRIORITY_LOW = 'LOW';
    const PRIORITY_MEDIUM = 'MED';
    const PRIORITY_HIGH = 'HIGH';
    
    const REVISION_PREVENTIVE = 'PREV';
    const REVISION_CORRECTIVE = 'CORR';
    
    const USER_PROFILE_TECH = 'TECH';
    const DEFAULT_USER_CODE = 'DFLTUSR';
    
    public $userName = null;
    private $_saveDetail = false;
    public $fileattachment = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incident';
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [
            'IdServiceCentre', 'IdReportUser', 'IdCategoryType', 'IdInterruptType', 'IdTitle'
            , 'IdPriorityType', 'IdRevisionType', 'IdState', 'Commentaries', 'IdUser', 'IdCreateUser'
            , 'Ticket', 'TicketDate', 'IncidentDate', 'IdSubCategoryType', 'IdResource'
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdServiceCentre', 'IdReportUser', 'IdCategoryType', 'IdInterruptType'
                , 'IdPriorityType', 'IdRevisionType', 'IdState', 'Commentaries', 'IdUser', 'IdCreateUser'], 'required'],
            [['IdServiceCentre', 'IdReportUser', 'IdCategoryType', 'IdInterruptType', 'IdTitle'
                , 'IdPriorityType', 'IdRevisionType', 'IdState', 'Commentaries', 'IdUser', 'IdCreateUser'], 'required'
                , 'on' => self::SCENARIO_CREATE ],
            [['Ticket', 'IdServiceCentre', 'IdReportUser', 'IdCategoryType', 'IdSubCategoryType', 'IdInterruptType'
                , 'IdPriorityType', 'IdRevisionType', 'IdState', 'IdUser', 'IdCreateUser', 'IdParentIncident'
                , 'IdIncidentRequest', 'IdTitle', 'IdResource']
                , 'integer'],
            [['IncidentDate', 'TicketDate', 'InterruptDate', 'SolutionDate'], 'safe'],
            [['Commentaries'],'string'],
            [['IdCategoryType'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentcategory::class, 'targetAttribute' => ['IdCategoryType' => 'Id']],
            [['IdSubCategoryType'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentcategory::class, 'targetAttribute' => ['IdSubCategoryType' => 'Id']],
            [['IdInterruptType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdInterruptType' => 'Id']],
            [['IdPriorityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdPriorityType' => 'Id']],
            [['IdReportUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdReportUser' => 'Id']],
            [['IdRevisionType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdRevisionType' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdCreateUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdCreateUser' => 'Id']],
            [['IdParentIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Incident::class, 'targetAttribute' => ['IdParentIncident' => 'Id']],
            [['IdIncidentRequest'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentrequest::class, 'targetAttribute' => ['IdIncidentRequest' => 'Id']],
            [['IdTitle'], 'exist', 'skipOnError' => true, 'targetClass' => Incidenttitle::class, 'targetAttribute' => ['IdTitle' => 'Id']],
            [['IdResource'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::class, 'targetAttribute' => ['IdResource' => 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdTitle' => Yii::t('app', 'Title'),
            'Ticket' => 'Ticket',
            'IncidentDate' => 'Fecha Incidente',
            'TicketDate' => 'Fecha Reporte',
            'InterruptDate' => 'Fecha Interrupción',
            'SolutionDate' => 'Fecha Solución',
            'IdServiceCentre' => 'Departamento',
            'IdReportUser' => 'Usuario que Reporta',
            'IdCategoryType' => 'Tipo Incidencia',
            'IdSubCategoryType' => 'SubTipo Incidencia',
            'IdInterruptType' => 'Tipo Interrupción',
            'IdPriorityType' => 'Prioridad',
            'IdRevisionType' => 'Tipo Revisión',
            'IdState' => 'Estado',
            'Commentaries' => 'Comentarios',
            'IdUser' => 'Usuario Asignado',
            'IdCreateUser' => 'Registrado por',
            'IdParentIncident' => 'Parent Ticket',
            'IdResource' => Yii::t('app', 'Resource'),
            'fileattachment' => Yii::t('app', 'Attachments'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentIncident()
    {
        return $this->hasOne(Incident::class, ['Id' => 'IdParentIncident']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResource()
    {
        return $this->hasOne(Resource::class, ['Id' => 'IdResource']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncidentRequest()
    {
        return $this->hasOne(Incidentrequest::class, ['Id' => 'IdIncidentRequest']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitle()
    {
        return $this->hasOne(Incidenttitle::class, ['Id' => 'IdTitle']);
    }

    /**
     * @return array
     */
    public function getTitles(){
        $titles = Incidenttitle::find()
            ->select(['Id', 'Title'])
            ->where([
                'Enabled' => Incidenttitle::ENABLED,
            ])
            ->orderBy(['Title' => SORT_ASC])
            ->asArray()->all();
        return ArrayHelper::map($titles, 'Id', 'Title');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryType()
    {
        return $this->hasOne(Incidentcategory::class, ['Id' => 'IdCategoryType']);
    }
    
    public function getCategoryTypes(){
        try {
            $droptions = Incidentcategory::find()
                    ->innerJoinWith('state b')
                    ->where(['b.Code'=> Incidentcategory::STATUS_ACTIVE])
                    ->andWhere(Incidentcategory::tableName().'.IdParent IS NULL')
                    ->asArray()
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategoryType()
    {
        return $this->hasOne(Incidentcategory::class, ['Id' => 'IdSubCategoryType']);
    }

    public function getSubCategoryTypes(){
        try {
            $droptions = Incidentcategory::find()
                ->innerJoinWith('state b')
                ->where([
                    Incidentcategory::tableName().'.IdParent' => $this->IdCategoryType,
                    'b.Code'=> Incidentcategory::STATUS_ACTIVE,
                ])
                ->andWhere( Incidentcategory::tableName().'.IdParent IS NOT NULL')
                ->asArray()
                ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterruptType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdInterruptType']);
    }
    
    public function getInterruptTypes(){
        try {
            $droptions = Type::find()
                    ->select([
                        Type::tableName().'.Id',
                        Type::tableName().'.Name',
                        Type::tableName().'.IdState'
                    ])
                    ->innerJoinWith('state b')
                    ->where([
                        Type::tableName().'.KeyWord'=>StringHelper::basename(self::class)."Interrupt",
                        'b.Code'=> Type::STATUS_ACTIVE
                    ])
                    ->orderBy([ Type::tableName().'.Id'=>'ASC' ])
                    ->asArray()
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriorityType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdPriorityType']);
    }
    
    public function getPriorityTypes(){
        try {
            $droptions = Type::find()
                    ->select([
                        Type::tableName().'.Id',
                        Type::tableName().'.Name',
                        Type::tableName().'.IdState'
                    ])
                    ->innerJoinWith('state b')
                    ->where([
                        Type::tableName().'.KeyWord'=>StringHelper::basename(self::class)."Priority",
                        'b.Code'=> Type::STATUS_ACTIVE
                    ])
                    ->orderBy([ Type::tableName().'.Id'=>'ASC' ])
                    ->asArray()
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportUser()
    {
        return $this->hasOne(User::class, ['id' => 'IdReportUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'IdCreateUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdRevisionType']);
    }
    
    public function getRevisionTypes(){
        try {
            $droptions = Type::find()
                    ->select([
                        Type::tableName().'.Id',
                        Type::tableName().'.Name',
                        Type::tableName().'.IdState'
                    ])
                    ->innerJoinWith('state b')
                    ->where([
                        Type::tableName().'.KeyWord'=>StringHelper::basename(self::class)."Revision",
                        'b.Code'=> Type::STATUS_ACTIVE
                    ])
                    ->orderBy([ Type::tableName().'.Id'=>'ASC'])
                    ->asArray()
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }
    
    public function getServicecentres(){
        try {
            $tableName = Servicecentre::tableName();
            $droptions = Servicecentre::find()
                    ->select(["$tableName.Id","$tableName.Name","$tableName.IdState","$tableName.MBCode"])
                    ->innerJoinWith('state b')
                    ->where(['b.Code'=> Servicecentre::STATE_ACTIVE])
                    ->orderBy(['MBCode'=>'ASC'])
                    ->asArray()
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
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
    public function getIncidentdetails()
    {
        return $this->hasMany(Incidentdetail::class, ['IdIncident' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return Attachment::findAll([
            'KeyWord' => StringHelper::basename(self::class),
            'AttributeName' => 'Id',
            'AttributeValue' => $this->Id,
        ]);
    }
    
    private function _generateNumTicket (){
        try {
            $date = date('ymd');
            $service = Servicecentre::find()->select('MBCode')->where(['Id'=> $this->IdServiceCentre])->scalar();
            $id = (int) $this->_getLastId() + 1;
            $this->Ticket = $date.str_pad($service,3,'0', STR_PAD_LEFT).str_pad($id,4,'0',STR_PAD_LEFT);
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
        try {
            $this->IncidentDate = Yii::$app->getFormatter()->asDate($this->IncidentDate, 'php:Y-m-d H:i:s');
            $this->TicketDate = Yii::$app->getFormatter()->asDate($this->TicketDate, 'php:Y-m-d H:i:s');
            $this->InterruptDate = $this->InterruptDate ? Yii::$app->getFormatter()->asDate($this->InterruptDate,'php:Y-m-d H:i:s'):$this->InterruptDate;
            $this->SolutionDate = $this->SolutionDate ? Yii::$app->getFormatter()->asDate($this->SolutionDate,'php:Y-m-d H:i:s'):$this->SolutionDate;
            
            if($this->isNewRecord){
                $this->_generateNumTicket();
                $this->IdState = State::find()->select('Id')->where(['KeyWord'=> StringHelper::basename(self::class),'Code'=> self::STATE_ASSIGNED])->scalar();
                $this->_assignDefaultUser();
                $this->_saveDetail = true;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_formatOutDate();
            if($this->_saveDetail){
                $this->refresh();
                $this->_createDefaultDetail();
            }
            !empty( $this->fileattachment ) ? $this->saveFiles() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterFind() {
        try {
            $this->_formatOutDate();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    private function _formatOutDate(){
        try {
            $this->IncidentDate = Yii::$app->getFormatter()->asDate($this->IncidentDate,'php:d-m-Y H:i:s');
            $this->TicketDate = Yii::$app->getFormatter()->asDate($this->TicketDate,'php:d-m-Y H:i:s');
            $this->InterruptDate = $this->InterruptDate ? Yii::$app->getFormatter()->asDate($this->InterruptDate,'php:d-m-Y H:i:s'): $this->InterruptDate;
            $this->SolutionDate = $this->SolutionDate ? Yii::$app->getFormatter()->asDate($this->SolutionDate,'php:d-m-Y H:i:s'): $this->SolutionDate;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function getTechnicians(){
        try {
            $tableName = strtolower( StringHelper::basename( User::class));
            $users = User::find()
                        ->select([
                            $tableName.'.Id',
                            $tableName.'.IdProfile',
                            $tableName.'.DisplayName',

                        ])
                        ->joinWith('profile b')
                        ->join('inner join', Setting::tableName().' c', 'c.KeyWord = :keyword AND c.Code = :code',[':keyword'=> StringHelper::basename(User::class),':code'=> self::USER_PROFILE_TECH])
                        ->join('inner join', Settingdetail::tableName().' d', 'd.IdSetting = c.Id')
                        ->join('inner join', State::tableName().' e', $tableName.'.IdState = e.Id')
                        ->where('d.Code = b.Code')
                        ->andWhere([
                            'e.Code' => User::STATE_ACTIVE,
                        ])
                        ->orderBy([$tableName.'.Id'=> 'ASC'])
                        ->asArray()
                        ->all()
                    ;
            return ArrayHelper::map($users, 'Id', 'DisplayName');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _assignDefaultUser(){
        try {
            $this->IdUser = $this->IdUser ?: Yii::$app->getUser()->getIdentity()->getId();;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _createDefaultDetail(){
        try {
            $detail = new Incidentdetail();
            $detail->IdIncident = $this->Id;
            $detail->Description = 'Asignación Automática de Incidencia';
            $detail->IdUser = $this->IdUser;
            $detail->IdAssignedUser = $this->IdUser;
            $detail->IdActivityType =
                Type::findOne([
                    'KeyWord' => StringHelper::basename(Incidentdetail::class).'Activity',
                    'Code' => Incidentdetail::ACTIVITY_ASSIGNMENT])->Id;
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
}
