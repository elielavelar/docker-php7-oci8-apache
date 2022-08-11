<?php

namespace backend\models;

use backend\models\traits\Incidentrequestdetailtrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

use common\models\Type;
use common\models\State;
use common\models\User;

/**
 * This is the model class for table "incidentrequestdetail".
 *
 * @property int $Id
 * @property int $IdIncidentRequest
 * @property int $IdActivityType
 * @property string $DetailDate
 * @property string $RecordDate
 * @property int $IdIncidentRequestState
 * @property string|null $Description
 * @property string|null $Commentaries
 * @property int $IdUser
 * @property int|null $IdAssignedUser
 *
 * @property Type $activityType
 * @property User $assignedUser
 * @property Incidentrequest $incidentRequest
 * @property State $incidentRequestState
 * @property User $user
 */
class Incidentrequestdetail extends \yii\db\ActiveRecord
{
    use Incidentrequestdetailtrait;
    protected $_newRecord = false;
    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_ASSIGNMENT = 'assignment';
    const SCENARIO_FOLLOWING = 'following';
    const SCENARIO_REASSIGNMENT = 'reassignment';
    const SCENARIO_SOLVED = 'solved';
    const SCENARIO_CLOSE = 'close';
    const SCENARIO_CANCEL = 'cancel';
    const SCENARIO_REJECT = 'reject';
    const SCENARIO_APPROVE = 'approve';

    const ACTIVITY_ASSIGNMENT = 'ASG';
    const ACTIVITY_FOLLOWING = 'FLLW';
    const ACTIVITY_SOLVED = 'RSV';
    const ACTIVITY_REASSIGNMENT = 'RASG';
    const ACTIVITY_CANCEL = 'CAN';
    const ACTIVITY_CLOSE = 'CLS';
    const ACTIVITY_REJECT = 'REJ';
    const ACTIVITY_APPROVE = 'APRV';

    const ASSIGNMENT_PROFILES = 'ASG';

    public $fileattachment = [];

    protected $activityState = [];
    protected $iconsByActivity = [];
    protected $classByActivity = [];
    protected $activityByState = [];
    protected $attributesByActivity = [];
    protected static $initialAttributes = [
        'Id', 'IdIncidentRequest','IdActivityType', 'RecordDate', 'DetailDate', 'Description',
        'IdUser', 'IdAssignedUser', 'fileattachment'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incidentrequestdetail';
    }

    function __construct($config = array()) {
        $this->activityState = [
            self::ACTIVITY_ASSIGNMENT => Incidentrequest::STATUS_REGISTRED,
            self::ACTIVITY_FOLLOWING => Incidentrequest::STATUS_INPROCESS,
            self::ACTIVITY_REASSIGNMENT => Incidentrequest::STATUS_INPROCESS,
            self::ACTIVITY_SOLVED => Incidentrequest::STATUS_CLOSED,
            self::ACTIVITY_CLOSE => Incidentrequest::STATUS_CLOSED,
            self::ACTIVITY_CANCEL => Incidentrequest::STATUS_REJECTED,
            self::ACTIVITY_APPROVE => Incidentrequest::STATUS_APPROVED,
            self::ACTIVITY_REJECT => Incidentrequest::STATUS_REJECTED,
        ];
        $this->iconsByActivity = [
            self::ACTIVITY_ASSIGNMENT => 'fas fa-user',
            self::ACTIVITY_FOLLOWING => 'fas fa-cogs',
            self::ACTIVITY_REASSIGNMENT => 'fas fa-people-arrows',
            self::ACTIVITY_SOLVED => 'fas fa-check-square',
            self::ACTIVITY_CLOSE => 'fas fa-archive',
            self::ACTIVITY_CANCEL => 'fas fa-times',
            self::ACTIVITY_APPROVE => 'fas fa-check-circle',
            self::ACTIVITY_REJECT => 'fas fa-times-circle',
        ];
        $this->classByActivity = [
            self::ACTIVITY_ASSIGNMENT => 'info',
            self::ACTIVITY_FOLLOWING => 'navy',
            self::ACTIVITY_REASSIGNMENT => 'warning',
            self::ACTIVITY_SOLVED => 'success',
            self::ACTIVITY_CLOSE => 'gray-dark',
            self::ACTIVITY_CANCEL => 'danger',
            self::ACTIVITY_REJECT => 'danger',
            self::ACTIVITY_APPROVE => 'success',
        ];

        $this->activityByState = [
            Incidentrequest::STATUS_REGISTRED => [
                self::ACTIVITY_FOLLOWING,
                self::ACTIVITY_REASSIGNMENT,
                self::ACTIVITY_CANCEL,
                self::ACTIVITY_SOLVED,
                self::ACTIVITY_APPROVE,
                self::ACTIVITY_REJECT,
            ],
            Incidentrequest::STATUS_INPROCESS => [
                self::ACTIVITY_FOLLOWING,
                self::ACTIVITY_REASSIGNMENT,
                self::ACTIVITY_CANCEL,
                self::ACTIVITY_SOLVED,
                self::ACTIVITY_APPROVE,
                self::ACTIVITY_REJECT,
            ],
            Incidentrequest::STATUS_APPROVED => [
                self::ACTIVITY_CLOSE,
            ],
            Incidentrequest::STATUS_CLOSED => [],
            Incidentrequest::STATUS_REJECTED => [
                self::ACTIVITY_CLOSE,
            ],
        ];
        return parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdIncidentRequest', 'IdActivityType', 'IdIncidentRequestState', 'IdUser', 'IdAssignedUser'], 'integer'],
            [['IdActivityType', 'IdUser', 'Description'], 'required'],
            [['DetailDate', 'RecordDate'], 'safe'],
            [['Description', 'Commentaries'], 'string'],
            [['IdActivityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdActivityType' => 'Id']],
            [['IdAssignedUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdAssignedUser' => 'Id']],
            [['IdIncidentRequest'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentrequest::class, 'targetAttribute' => ['IdIncidentRequest' => 'Id']],
            [['IdIncidentRequestState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdIncidentRequestState' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('app', 'ID'),
            'IdIncidentRequest' => Yii::t('system', 'Service Request'),
            'IdActivityType' => Yii::t('app', 'Id Activity Type'),
            'DetailDate' => Yii::t('app', 'Detail Date'),
            'RecordDate' => Yii::t('app', 'Record Date'),
            'IdIncidentRequestState' => Yii::t('app', 'Id Incident Request State'),
            'Description' => Yii::t('app', 'Description'),
            'Commentaries' => Yii::t('app', 'Commentaries'),
            'IdUser' => Yii::t('app', 'Id User'),
            'IdAssignedUser' => Yii::t('app', 'Id Assigned User'),
            'fileattachment' => Yii::t('app', 'Attachments'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivityType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdActivityType']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws Exception
     */
    public function getActivityTypes()
    {
        $query = Type::find()
            ->select(['Id', 'Name', 'Code'])
            ->where([
                'KeyWord' => StringHelper::basename(self::class).'Activity',
            ]);
        if($this->IdIncidentRequest){
            $query->andWhere([
                'Code' => ArrayHelper::getValue( $this->activityByState, $this->incidentRequest->state->Code, [])
            ]);
        }
        $types = $query
            ->orderBy([
                'Sort' => SORT_ASC
            ])
            ->asArray()->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdAssignedUser']);
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
    public function getIncidentRequestState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdIncidentRequestState']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }

    public function getTechnicians(){
        try {
            return ( new Incidentrequest())->getTechnicians();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function beforeSave($insert) {
        try {
            $this->RecordDate = $this->RecordDate ?
                Yii::$app->getFormatter()->asDate($this->RecordDate, 'php:Y-m-d H:i:s') :
                $this->RecordDate;
            $this->DetailDate = $this->DetailDate ?
                Yii::$app->getFormatter()->asDate($this->DetailDate, 'php:Y-m-d H:i:s') :
                $this->DetailDate;
            $this->_newRecord = $this->isNewRecord;
            $this->isNewRecord ?
                $this->_verifyApplication()
                : null;
        } catch (Exception $ex) {
            if($this->hasErrors()){
                return false;
            } else {
                throw $ex;
            }
        }
        return parent::beforeSave($insert);
    }

    private function _setState(){
        try {
            $this->IdIncidentRequestState = State::find()->where([
                'KeyWord' => StringHelper::basename( Incidentrequest::class),
                'Code' => ArrayHelper::getValue($this->activityState
                    , ($this->IdActivityType
                        ? $this->activityType->Code
                        : self::ACTIVITY_FOLLOWING ), self::ACTIVITY_FOLLOWING),
            ])->select(['Id'])->scalar();
        } catch( Exception $exception ){
            throw $exception;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_newRecord ?
                $this->_updateParentIncident()
                : null;
            !empty( $this->fileattachment ) ? $this->saveFiles() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    private function _verifyApplication(){
        try {
            $this->_setState();
            if( $this->activityType->Code == self::ACTIVITY_APPROVE ){
                if(!$this->incidentRequest->validateIncident()){
                    $this->addError('IdActivityType', $this->getAttributeLabel('IdIncidentRequest').': '.Yii::$app->customFunctions->getErrors($this->incidentRequest->getErrors(), false));
                    Yii::$app->appLog->setLog($this->incidentRequest->getErrors(),  'error');
                    throw new Exception($this->getError('IdActivityType'), 9099);
                }
            }
        } catch (Exception $exception){
            throw $exception;
        }
    }

    private function _applyRequest(){
        try {
            if( $this->activityType->Code == self::ACTIVITY_APPROVE ){
                if(!$this->incidentRequest->apply()){
                    $this->addError('IdIncidentRequest', Yii::$app->customFunctions->getErrors($this->incidentRequest->getErrors()));
                    Yii::$app->appLog->setLog($this->incidentRequest->getErrors(),  'error');
                    throw new Exception($this->getError('IdIncidentRequest'), 9099);
                }
            }
        } catch (Exception $exception){
            throw $exception;
        }
    }

    private function _updateParentIncident()
    {
        try {
            $this->refresh();
            $this->_verifyApplication();
            $this->_applyRequest();
            $this->incidentRequest->IdUser = $this->IdAssignedUser;
            $this->incidentRequest->IdState = $this->IdIncidentRequestState;
            if(!$this->incidentRequest->save()){
                $this->addError('IdIncidentRequest', Yii::$app->customFunctions->getErrors($this->incidentRequest->getErrors()));
                Yii::$app->appLog->setLog($this->incidentRequest->getErrors(),  'error');
            }

        } catch (Exception $exception){
            throw $exception;
        }
    }
}
