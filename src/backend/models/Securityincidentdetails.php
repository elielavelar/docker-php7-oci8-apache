<?php

namespace backend\models;

use Yii;
use common\models\State;
use common\models\Type;
use common\models\User;
use backend\models\Activetype;
use backend\models\Problemtype;
use backend\models\Securityincident;
use common\models\Servicecentres;
use backend\models\Attachments;
use common\models\Catalogdetailvalues;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "securityincidentdetails".
 *
 * @property int $Id
 * @property int $IdSecurityIncident
 * @property string $Title
 * @property string $Description
 * @property string $DetailDate
 * @property string $RecordDate
 * @property string $SolutionDate
 * @property int $IdUser
 * @property int $IdActiveType
 * @property int $IdProblemType
 * @property int $IdActivityType
 * @property int $IdAssignedUser
 * @property int $IdIncidentState
 * @property int $IdCatalogDetailValue
 * @property string $Commentaries
 * @property string $Investigation
 * @property string $KnowledgeBase
 *
 * @property Type $activityType
 * @property User $assignedUser
 * @property Activetype $activeType
 * @property Problemtype $problemType
 * @property Securityincident $securityIncident
 * @property State $incidentState
 * @property User $user
 * @property Catalogdetailvalues $catalogDetailValue
 * @property Attachments[] $attachments
 */
class Securityincidentdetails extends \yii\db\ActiveRecord
{
    
    const ACTIVITY_ASSIGNMENT = 'ASG';
    const ACTIVITY_FOLLOWING = 'FLLW';
    const ACTIVITY_REASSIGNMENT = 'RASG';
    const ACTIVITY_CLOSE = 'CLS';
    const ACTIVITY_EVALUATION = 'EVAL';
    
    const ASSIGNMENT_PROFILES = 'ASG';
    private $activityState = [];
    
    function __construct($config = array()) {
         $this->activityState = [
            self::ACTIVITY_ASSIGNMENT => Securityincident::STATE_REGISTRED,
            self::ACTIVITY_FOLLOWING => Securityincident::STATE_INPROCESS,
            self::ACTIVITY_REASSIGNMENT => Securityincident::STATE_INPROCESS,
            self::ACTIVITY_EVALUATION => Securityincident::STATE_CLOSED,
            self::ACTIVITY_CLOSE => Securityincident::STATE_SOLVED,
        ];
        return parent::__construct($config);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'securityincidentdetails';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['RecordDate','DetailDate'],
                ],
                'value'=>new Expression('NOW()'),
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdSecurityIncident', 'IdUser', 'IdActivityType', 'IdAssignedUser'], 'required'],
            [['IdSecurityIncident', 'IdUser', 'IdActivityType', 'IdAssignedUser', 'IdIncidentState','IdCatalogDetailValue'], 'integer'],
            [['Description', 'Commentaries', 'Investigation', 'KnowledgeBase'], 'string'],
            [['DetailDate', 'RecordDate', 'SolutionDate'], 'safe'],
            [['Title'], 'string', 'max' => 250],
            [['IdActivityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdActivityType' => 'Id']],
            [['IdAssignedUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdAssignedUser' => 'id']],
            [['IdSecurityIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Securityincident::className(), 'targetAttribute' => ['IdSecurityIncident' => 'Id']],
            [['IdIncidentState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdIncidentState' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdCatalogDetailValue'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetailvalues::className(), 'targetAttribute' => ['IdCatalogDetailValue' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdSecurityIncident' => 'Ticket Incidente',
            'Title' => 'Título',
            'Description' => 'Descripción',
            'DetailDate' => 'Fecha Detalle',
            'RecordDate' => 'Fecha Registro',
            'SolutionDate' => 'Fecha Solución',
            'IdUser' => 'Usuario',
            'IdActivityType' => 'Tipo Actividad',
            'IdAssignedUser' => 'Usuario Asignado',
            'IdIncidentState' => 'Estado',
            'IdCatalogDetailValue' => 'Evaluación',
            'Commentaries' => 'Comentarios',
            'Investigation' => 'Investigación',
            'KnowledgeBase' => 'Base de Conocimiento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivityType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdActivityType']);
    }
    
    public function getActivityTypes($criteria = []){
        if($this->securityIncident->IdState){
            $_code= $this->securityIncident->state->Code;
            $activity = NULL; 
            foreach ($this->activityState as $st => $value){
                $activity = ($value == $_code && $activity == NULL)? $st:$activity;
            }
            $val = Type::findOne(['KeyWord'=> StringHelper::basename(self::class).'Activity','Code'=> $activity]);
            $criteria = ['>=','CAST(type.Value as UNSIGNED)',(int)$val->Value];
        }
        $droptions = Type::find()
                ->joinWith('state b')
                ->where([
                    'b.KeyWord' => StringHelper::basename(Type::class),
                    'b.Code' => Type::STATUS_ACTIVE,
                    'type.KeyWord' => StringHelper::basename(self::class).'Activity',
                ])->andWhere($criteria)
                ->orderBy(['type.Value' => SORT_ASC])
                ->asArray()->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdAssignedUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecurityIncident()
    {
        return $this->hasOne(Securityincident::className(), ['Id' => 'IdSecurityIncident']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetailValue()
    {
        return $this->hasOne(Catalogdetailvalues::className(), ['Id' => 'IdCatalogDetailValue']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncidentState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdIncidentState']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdUser']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachments::className(), ['KeyWord' => StringHelper::basename(self::class), 'AttributeName' => 'Id', 'AttributeValue' => $this->Id]);
    }
    
    public function assignDefaultUser(){
        try {
            $this->_assignDefaultUser();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        try {
            if($this->isNewRecord){
		$this->Title = $this->IdActivityType ? $this->activityType->Name : '';
                !$this->IdIncidentState ? $this->_setState():NULL;
            } else {
			$this->DetailDate = !empty($this->DetailDate) ? \Yii::$app->getFormatter()->asDate($this->DetailDate ,'php:Y-m-d H:i:s') : $this->DetailDate;
		$this->RecordDate = !empty($this->RecordDate) ? \Yii::$app->getFormatter()->asDate($this->RecordDate ,'php:Y-m-d H:i:s') : $this->RecordDate;
		}
            $this->SolutionDate = !empty($this->SolutionDate) ? \Yii::$app->getFormatter()->asDate($this->SolutionDate,'php:Y-m-d H:i:s') : $this->SolutionDate;

        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->SolutionDate = !empty($this->SolutionDate) ? \Yii::$app->getFormatter()->asDate($this->SolutionDate,'php: d-m-Y H:i:s') : $this->SolutionDate;
            if($this->IdIncidentState){
                $secIncident = Securityincident::findOne(['Id'=> $this->IdSecurityIncident]);
                $secIncident->IdState = $this->IdIncidentState;
                $secIncident->IdUser = $this->IdAssignedUser;
                if($this->SolutionDate){
                    $secIncident->SolutionDate = $secIncident->SolutionDate ? $secIncident->SolutionDate : $this->SolutionDate;
                }
                $secIncident->save();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    private function _assignDefaultUser(){
        try {
            $setting = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord' => StringHelper::basename(Securityincident::class)
                            ,'b.Code' => Securityincident::DEFAULT_USER_CODE
                            ,'settingsdetail.Code' => Securityincident::DEFAULT_USER_CODE
                        ])
                    ->one();
            if(!empty($setting)){
                $user = User::findOne(['Username' => $setting->Value]);
                $_user = !empty($user) ? $user->Id : Yii::$app->user->getIdentity()->getId();
            } else {
                $_user = Yii::$app->user->getIdentity()->getId();
            }
            $this->IdAssignedUser = $_user;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getUserAssignment(){
        try {
            $users = User::find()
                    ->joinWith('profile b')
                    ->where("( b.Code IN( SELECT c.Code FROM settingsdetail c"
                            . " INNER JOIN settings d ON d.Id = c.IdSetting WHERE d.KeyWord = :keyw AND d.Code = :scode)"
                            . " OR (user.IdServiceCentre = :servicecentre AND b.Code IN(:boss, :aux) ) )"
                            , [
                                ':keyw' => StringHelper::basename(self::class),
                                ':scode' => self::ASSIGNMENT_PROFILES,
                                ':servicecentre' => $this->securityIncident->IdServiceCentre,
                                ':boss' => Servicecentres::PROFILE_CHIEF_SERVICECENTRE_DUISITE,
                                ':aux' => Servicecentres::PROFILE_AUXCHIEF_SERVICECENTRE_DUISITE,
                            ])->all();
            return ArrayHelper::map($users, 'Id', 'DisplayName');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setState(){
        try {
            $_code = $this->IdActivityType ? $this->activityState[$this->activityType->Code]:$this->activityState[self::ACTIVITY_ASSIGNMENT];
            $state = State::find()
                    ->where([
                        'KeyWord' => StringHelper::basename(Securityincident::class),
                        'Code'=> $_code
                    ])->one();
            if(!empty($state)){
                $this->IdIncidentState = $state->Id;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setStateParent(){
        try {
            $this->_setState();
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
