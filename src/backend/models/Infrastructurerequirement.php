<?php

namespace backend\models;

use Yii;
use backend\models\Infrastructurerequirementtype;
use backend\models\Incident;
use common\models\User;
use common\models\State;
use common\models\Type;
use common\models\Servicecentre;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "infrastructurerequirement".
 *
 * @property int $Id
 * @property string $Ticket
 * @property string $TicketDate
 * @property string $RequirementDate
 * @property string $SolutionDate
 * @property string $Title
 * @property int $IdServiceCentre
 * @property int $IdIncident
 * @property int $IdState
 * @property int $IdInfrastructureRequirementType
 * @property int $IdReportUser
 * @property int $IdUser
 * @property int $AffectsFunctionality
 * @property int $AffectsSecurity
 * @property int $Quantity
 * @property string $DamageDescription
 * @property int $IdPriorityType
 * @property string $SpecificLocation
 * @property string $Description
 * @property int $IdCreateUser
 * @property int $VerificationStatus
 * @property int $IdVerficationUser
 * @property string $VerficationDate
 *
 * @property User $createUser
 * @property Incident $incident
 * @property Type $priorityType
 * @property User $reportUser
 * @property User $verificationUser
 * @property Servicecentre $serviceCentre
 * @property State $state
 * @property Infrastructurerequirementtype $infrastructureRequirementType
 * @property User $user
 * @property Infrastructurerequirementdetails[] $infrastructurerequirementdetails
 */
class Infrastructurerequirement extends \yii\db\ActiveRecord
{
    const STATE_PENDENT = 'PNDT';
    const STATE_INPROCESS = 'PROC';
    const STATE_REPAIRED = 'REPD';
    const STATE_UNREPAIRED = 'SLV';
    const STATE_CLOSED = 'CLS';
    
    const PRIORITY_LOW = 'LOW';
    const PRIORITY_MEDIUM = 'MED';
    const PRIORITY_HIGH = 'HIGH';
    
    const AFFECTS_FUNCTIONALITY_DISABLE = 0;
    const AFFECTS_FUNCTIONALITY_ENABLE = 1;
    
    const AFFECTS_SECURITY_DISABLE = 0;
    const AFFECTS_SECURITY_ENABLE = 1;
    
    const VERIFICATION_STATUS_DISABLE = 0;
    const VERIFICATION_STATUS_ENABLE = 1;
    
    const DEFAULT_QUANTITY = 1;
    const DEFAULT_USER_CODE = 'DFLTUSR';
    const DEFAULT_USER_CODE_REQUIREMENT = 'DFLTUSR';
    const DEFAULT_USER_CODE_SERVICE = ' SRVCUSR';
    
    private $_saveDetail = false;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infrastructurerequirement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Title', 'IdServiceCentre', 'IdState', 'IdInfrastructureRequirementType', 'IdReportUser', 'IdPriorityType', 'SpecificLocation', 'IdCreateUser'], 'required'],
            [['Ticket', 'IdServiceCentre', 'IdIncident', 'IdState', 'IdInfrastructureRequirementType', 'IdReportUser', 'IdUser', 'AffectsFunctionality', 'AffectsSecurity', 'Quantity'
                , 'IdPriorityType', 'IdCreateUser','IdVerificationUser', 'VerificationStatus'], 'integer'],
            [['TicketDate', 'RequirementDate', 'SolutionDate','VerificationDate'], 'safe'],
            [['DamageDescription', 'Description'], 'string'],
            [['Title'], 'string', 'max' => 250],
            [['SpecificLocation'], 'string'],
            [['IdCreateUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdCreateUser' => 'Id']],
            [['IdIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Incident::className(), 'targetAttribute' => ['IdIncident' => 'Id']],
            [['IdPriorityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdPriorityType' => 'Id']],
            [['IdReportUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdReportUser' => 'Id']],
            [['IdVerificationUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdVerificationUser' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::className(), 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdInfrastructureRequirementType'], 'exist', 'skipOnError' => true, 'targetClass' => Infrastructurerequirementtype::className(), 'targetAttribute' => ['IdInfrastructureRequirementType' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
            ['AffectsFunctionality','default','value' => self::AFFECTS_FUNCTIONALITY_DISABLE],
            [['AffectsFunctionality'],'in','range'=>[self::AFFECTS_FUNCTIONALITY_DISABLE, self::AFFECTS_FUNCTIONALITY_ENABLE]],
            ['AffectsSecurity','default','value' => self::AFFECTS_SECURITY_DISABLE],
            [['AffectsSecurity'],'in','range'=>[self::AFFECTS_SECURITY_DISABLE, self::AFFECTS_SECURITY_ENABLE]],
            ['Quantity','default','value' => self::DEFAULT_QUANTITY],
            [['VerificationStatus'],'in','range'=>[self::VERIFICATION_STATUS_DISABLE, self::VERIFICATION_STATUS_ENABLE]],
            ['VerificationStatus','default','value' => self::VERIFICATION_STATUS_ENABLE],
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
            'TicketDate' => 'Fecha Ticket',
            'RequirementDate' => 'Fecha Requerimiento',
            'SolutionDate' => 'Fecha Solución',
            'Title' => 'Título',
            'IdServiceCentre' => 'Departamento / Duicentro ',
            'IdIncident' => 'Incidente Help Desk',
            'IdState' => 'Estado',
            'IdInfrastructureRequirementType' => 'Tipo',
            'AffectsFunctionality' => 'Afecta Funcionalidad',
            'AffectsSecurity' => 'Afecta Seguridad Ocup.',
            'Quantity' => 'Cantidad',
            'DamageDescription' => 'Descripción de Daño',
            'IdPriorityType' => 'Prioridad',
            'SpecificLocation' => 'Ubicación',
            'Description' => 'Comentarios',
            'IdUser' => 'Usuario Asignado',
            'IdReportUser' => 'Usuario Reporta',
            'IdCreateUser' => 'Usuario Creación',
            'IdVerificationUser' => 'Usuario Verificación',
            'VerificationStatus' => 'Verificado',
            'VerificationDate' => 'Fecha Verificación',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdCreateUser']);
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
    public function getpriorityType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdPriorityType']);
    }
    
    public function getpriorityTypes()
    {
        $model = Type::find()
                ->joinWith('state b')
                ->where([
                    'type.KeyWord' => StringHelper::basename(self::class).'Priority',
                    'b.Code' => Type::STATUS_ACTIVE,
                ])
                ->orderBy(['type.Id' => SORT_ASC])
                ->all();
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdReportUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::className(), ['Id' => 'IdServiceCentre']);
    }

    public function getServiceCentres()
    {
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
        $model = State::findAll(['KeyWord' => StringHelper::basename(Infrastructurerequirement::class)]);
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructureRequirementType()
    {
        return $this->hasOne(Infrastructurerequirementtype::className(), ['Id' => 'IdInfrastructureRequirementType']);
    }
    
    public function getInfrastructureRequirementTypes()
    {
        $model = Infrastructurerequirementtype::find()
                ->joinWith('state b')
                ->where([
                    'b.Code' => Infrastructurerequirementtype::STATUS_ACTIVE,
                ])
                ->andWhere('IdParent IS NOT NULL')
                ->orderBy(['IdParent' =>SORT_ASC])
                ->all();
        $result = ArrayHelper::map($model, 'Id', 'Name', function($model){
                return $model->IdParent ? $model->parent->Name : '';
        });
        return $result;
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
    public function getVerficationuser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdVerificationUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructurerequirementdetails()
    {
        return $this->hasMany(Infrastructurerequirementdetails::className(), ['IdInfrastructureRequirement' => 'Id']);
    }
    
    private function _generateNumTicket (){
        try {
            $date = date('ymd');
            $service = Servicecentre::find()->where(['Id'=> $this->IdServiceCentre])->one();
            $id = (int) $this->_getLastId() + 1;
            $this->Ticket = $date.str_pad($service->MBCode,3,'0', STR_PAD_LEFT).str_pad($id,4,'0',STR_PAD_LEFT);
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
    
    private function _assignDefaultUser(){
        try {
            $usercode = null;
            if($this->IdPriorityType ? $this->priorityType->Code == self::PRIORITY_HIGH : false){
                $usercode = self::DEFAULT_USER_CODE_SERVICE;
            } else {
                $requirementType = $this->IdInfrastructureRequirementType ? $this->infrastructureRequirementType->IdServiceCentre : null;
            }
            $setting = Settingdetail::find()
                        ->joinWith('setting b')
                        ->where(['b.KeyWord' => StringHelper::basename(self::class)
                                ,'b.Code' => self::DEFAULT_USER_CODE
                                , Settingdetail::tableName().'.Code' => $usercode
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
            $detail = new Infrastructurerequirementdetails();
            $detail->IdInfrastructureRequirement = $this->Id;
            $detail->Title = 'Asignación Automática de Requerimiento';
            $detail->IdUser = $this->IdUser;
            $detail->IdAssignedUser = $this->IdUser;
            $detail->IdActivityType = Type::findOne(['KeyWord' => StringHelper::basename(Infrastructurerequirementdetails::class).'Activity','Code' => Securityincidentdetails::ACTIVITY_ASSIGNMENT])->Id;
            $detail->IdRequirementState = $this->IdState;
            if(!$detail->save()){
                $errors = $detail->getErrors();
                $message = Yii::$app->customFunctions->getErrors($errors);
                throw new Exception($message, 94000);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        $this->TicketDate = Yii::$app->getFormatter()->asDate($this->TicketDate, 'php:Y-m-d H:i:s');
        $this->RequirementDate = $this->RequirementDate ? Yii::$app->getFormatter()->asDate($this->RequirementDate,'php:Y-m-d H:i:s'):$this->RequirementDate;
        $this->SolutionDate = $this->SolutionDate ? Yii::$app->getFormatter()->asDate($this->SolutionDate,'php:Y-m-d H:i:s'):$this->SolutionDate;

        if($this->isNewRecord){
            $this->_generateNumTicket();
            $this->IdState = State::findOne(['KeyWord'=> StringHelper::basename(self::class),'Code'=> self::STATE_PENDENT])->Id;
            $this->_assignDefaultUser();
            $this->_saveDetail = true;
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
    
    private function sendConfirmationMail($model, $action){
        try {
            $subject = '';
            $state = '';
            $url = Url::to(\Yii::$app->params["mainSiteUrl"]["url"]);
            if($action == 'create'){
                $subject = 'Asignación';
                $state = 'Registrada';
            } elseif($action == 'update'){
                $subject = 'Actualización';
                $state = 'Reprogramada';
            } elseif($action == 'cancel'){
                $subject = 'Cancelación';
                $state = 'Cancelada';
            }
            $body = '<ul> '
                    . '</ul>';
            $footer = "<br/>"
                    ;
            $content = [
                'title'=>$subject.' de Requerimiento',
                'body'=>$body,
                'footer'=>$footer,
            ];
            $email = Yii::$app
                ->mailer
                ->compose(
                    ['html' => '@frontend/mail/default-html'],
                    ['data' => $content]
                )
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($model->idCitizen->Email)
                ->setSubject($content['title'])
                ->send();
            
            if($email){
                #Yii::$app->getSession()->setFlash('success','Revisa la Bandeja de tu Email!');
            } else{
                Yii::$app->getSession()->setFlash('warning','Error al enviar confirmación, contacte al Administrador!');
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}