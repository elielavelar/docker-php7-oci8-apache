<?php
namespace common\models;

use common\models\traits\Usertrait;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use backend\components\AuthorizationFunctions;
use kartik\password\StrengthValidator;
use Exception;

use common\models\State;
use common\models\Profile;
use backend\models\Profileoption;
use backend\models\Settingdetail;
use backend\models\Setting;
use backend\models\Option;
use backend\models\Useroption;
use common\models\Userpreference;
use common\models\Attachment;

/**
 * User model
 *
 * @property int $Id
 * @property string $Username
 * @property string $FirstName
 * @property string $SecondName
 * @property string $LastName
 * @property string $SecondLastName
 * @property string $DisplayName
 * @property string $PasswordHash
 * @property string $PasswordResetToken
 * @property string $Email
 * @property string $AuthKey
 * @property string $TempToken
 * @property string $CreateDate
 * @property string $UpdateDate
 * @property string $PasswordExpirationDate
 * @property int $IdState
 * @property int $IdProfile
 * @property int $IdServiceCentre
 * @property string $DocumentNumber
 * @property string $Birthdate
 * @property string $password write-only password
 *
 * @property State $state
 * @property Profile $profile
 * @property Option[] $options
 * @property Useroption[] $useroptions;
 * @property Userpreference[] $userpreferences
 * @property Companyuser[] $companiesuser
 * @property Settingdetail[] $settingDetails
 * @property Attachment $attachmentPicture
 * @property Servicecentre $serviceCentre
 */
class User extends ActiveRecord implements IdentityInterface
{
    use Usertrait;
    public $profileName;
    public $completeName;
    public $serviceCentreName;
    public $stateName;
    public $settings = [];
    public $operator = NULL;
    public $updateOperator = false;
    public $syncPass = false;
    private $company = null;
    private $_customPassword = FALSE;
    private $_new = FALSE;
    public $uploadFile;
    
    private $auth;
    
    public $disabled = FALSE;
    public $expired = FALSE;
    public $remainingDays = 0;
    public $warningPass = FALSE;
    
    private $_role = NULL;
    
    public $_password = NULL;
    public $_passwordconfirm = NULL;
    
    public $menuItems = [];
    public $usersetting = NULL;
    public $_emptyUserOptions = FALSE;
    public $path = null;
    public $photo = null;
    const DEFAULT_IMG = 'img/avatar.png';

    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    
    const USER_ADMINISTRATOR = 'ADMIN';
    const PROFILE_ADMINISTRATOR = 'ADMIN';
    const PROFILE_USER = 'USER';
    const PROFILE_SUPERVISOR = 'SPVR';
    const TYPE_PERMANENT = 'PRM';
    const TYPE_TEMPORAL = 'SRV';
    
    const DEFAULT_PROFILE = 'USER';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_CONSOLE = 'console';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_DETAIL = 'detail';
    const SCENARIO_WEBSERVICE = 'webservice';
    const SCENARIO_UPLOAD = 'upload';
    
    const CLASS_USER_BACKEND = 'User';
    const CLASS_USER_FRONTEND = 'User';
    
    const PASSWORD_EXPIRATION_PARAMETER = 'PASSEXP';
    const PASSWORD_EXPIRATION_WARNING_PARAMETER = 'WRNPASS';
    const DOCUMENT_NUMBER_MASK = '99999999-9';
    const CODEMPLOYEE_MASK = '99999';
    const DEFAULT_PASS_EXPIRATION_DAYS = 120;
    const DEFAULT_PASS_LENGTH = 12;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function getTableName(){
        return preg_replace("/[^a-zA-Z]/", "", self::tableName() );
    }
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['Id','Username','FirstName','SecondName','LastName','SecondLastName','DisplayName'
            ,'Email','_password','_passwordconfirm','IdProfile', 'IdServiceCentre','profileName'
            ,'IdState'
        ];
        $scenarios[self::SCENARIO_LOGIN] = ['Username','_password','IdState','PasswordExpirationDate'];
        $scenarios[self::SCENARIO_WEBSERVICE] = ['Username','_password','AuthKey','IdState','PasswordExpirationDate'];
        $scenarios[self::SCENARIO_UPLOAD] = ['FirstName','SecondName','LastName','SecondLastName','DocumentNumber','Birthdate'];
        return $scenarios;
    }
    
    function __construct($config = array()) {
        $this->syncPass = isset(Yii::$app->params['system']['allowPassSync']) ? Yii::$app->params['system']['allowPassSync'] : false;
        if( StringHelper::basename(Yii::getAlias('@app')) != 'console'){
            $this->auth = new AuthorizationFunctions();
        } else {
            $this->scenario = self::SCENARIO_CONSOLE;
        }
        return parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['CreateDate'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['UpdateDate'],
                ],
                'value'=>new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['FirstName','LastName','IdState','IdProfile', 'IdServiceCentre','Username','DisplayName'],'required','message'=>'Campo {attribute} no puede quedar vacío'],
            [['IdState','IdProfile', 'IdServiceCentre'],'integer'],
            [['Email'],'email'],
            [['_password','_passwordconfirm'],'required','on'=>['create']],
            [['Username'],'unique','message'=>'{attribute} {value} ya existe'],
            [['CreateDate', 'UpdateDate','PasswordExpirationDate'], 'safe'],
            [['IdState'], 'default','value'=>  State::findOne(['KeyWord'=>'User','Code'=>  self::STATE_ACTIVE])->Id],
            [['IdProfile'], 'default','value'=> Profile::findOne(['Code'=>  self::DEFAULT_PROFILE])->Id],
            [['Username','FirstName','LastName', 'IdState'], 'required','message'=>'{attribute} no puede quedar vacío'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdProfile'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::class, 'targetAttribute' => ['IdProfile' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            ['_password', 'string', 'min' => 8],
            ['Username', 'string', 'min' => 4],
            [['Username','DisplayName'], 'string', 'max' => 50],
            [['AuthKey','TempToken'], 'string'],
            [['DocumentNumber'],'string','max' => 10],
            [['DocumentNumber'],'unique'],
            [['Username'], 'unique'],
            [['FirstName','SecondName','LastName','SecondLastName'], 'string','max'=>50],
            ['_passwordconfirm', 'string', 'min' => 8],
            ['_password', StrengthValidator::class,'preset'=>'normal','userAttribute'=>'Username'],
            ['_passwordconfirm', 'compare', 'compareAttribute'=>'_password', 'message'=>"Contraseñas no coinciden" ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Username' => Yii::t('system','Username'),
            'FirstName' => Yii::t('system','First Name'),
            'SecondName' => Yii::t('system','Second Name'),
            'LastName' => Yii::t('system','Last Name'),
            'SecondLastName' => Yii::t('system','Second Last Name'),
            'completeName' => Yii::t('system','Name'),
            'DisplayName' => Yii::t('system','DisplayName'),
            'IdState' => Yii::t('system','IdState'),
            'stateName' => Yii::t('system','IdState'),
            'IdProfile' => Yii::t('system','Profile'),
            'IdServiceCentre' =>  Yii::t('system','IdServiceCentre'),
            'profileName' => 'Perfil',
            'AuthKey' => 'Llave',
            'Email' => 'Email',
            'PasswordHash' => 'Contraseña',
            'CreateDate' => 'Fecha Creación',
            'UpdateDate' => 'Fecha Actualización',
            '_password' => 'Contraseña',
            '_passwordconfirm' => 'Confirmar Contraseña',
            'PasswordExpirationDate'=>'Fecha Expiración Contraseña',
            'uploadFile' => 'Archivo de Carga',
            'photo' => 'Fotografía',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['Id' => $id, 'IdState' => State::findOne(['KeyWord'=>'User','Code'=>self::STATE_ACTIVE])->Id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByTempAccessToken($token)
    {
        return self::find()->where(['TempToken' => $token])->andWhere('TempToken IS NOT NULL')->one();
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAuthKey($auth_key){
        return static::findOne(['AuthKey'=>$auth_key]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['Username' => $username, 'IdState' => State::findOne(['KeyWord'=>'User','Code'=>self::STATE_ACTIVE])->Id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre() : ActiveQuery
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }

    public function getServiceCentres() : array {
        try {
            $droptions = Servicecentre::find()
                ->joinWith('state b')
                ->where([
                    'b.Code'=> Servicecentre::STATE_ACTIVE
                ])->asArray()
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
    
    public function getStates(){
        try {
            $droptions = State::findAll(['KeyWord'=>'User']);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    

    /**
    * @return \yii\db\ActiveQuery
    */
   public function getUseroptions()
   {
       return $this->hasMany(Useroption::class, ['IdUser' => 'Id']);
   }
    
    /**
    * @return \yii\db\ActiveQuery
    */
   public function getUserpreferences()
   {
       return $this->hasMany(Userpreference::class, ['IdUser' => 'Id']);
   }
   
   /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(Option::class, ['Id' => 'IdOption'])->viaTable(Useroption::tableName(), ['IdUser' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingDetails()
    {
        return $this->hasMany(Settingdetail::class, ['Id' => 'IdSettingDetail'])->viaTable(Userpreference::tableName(), ['IdUser' => 'Id']);
    }
    
    public function getSettings(){
        try {
            $this->settings = Settingdetail::find()
                                ->joinWith('setting b')
                                ->joinWith('state c')
                                ->where(['b.KeyWord' => StringHelper::basename(Userpreference::class), 'c.Code' => Settingdetail::STATUS_ACTIVE])
                                ->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['Id' => 'IdProfile']);
    }

    
    public function getProfiles(){
        try {
            $droptions = Profile::find()
                ->select([Profile::tableName().".Id",Profile::tableName().".Name",Profile::tableName().".IdState"])
                ->innerJoin(State::tableName().' b')
                ->where(['b.Code'=> Profile::STATE_ACTIVE])
                ->orderBy([Profile::tableName().'.Id'=>'ASC'])
                ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'PasswordResetToken' => $token,
            'Idstate' => State::findOne(['KeyWord'=>'User','Code'=>self::STATE_ACTIVE])->Id
            #'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->AuthKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->PasswordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->PasswordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->AuthKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->PasswordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->PasswordResetToken = null;
    }

    public function beforeSave($insert) {
        try {
            $this->FirstName = $this->FirstName;
            $this->SecondName = $this->SecondName;
            $this->LastName = $this->LastName;
            $this->SecondLastName = $this->SecondLastName;
            $this->Username = strtoupper($this->Username);
            $this->DisplayName = empty($this->DisplayName) ? $this->LastName.(!empty($this->FirstName) ? $this->FirstName[0]:'') : $this->DisplayName;
            $this->CreateDate = $this->CreateDate ? Yii::$app->getFormatter()->asDate($this->CreateDate, 'php:Y-m-d H:i:s') : $this->CreateDate;
            $this->Birthdate = $this->Birthdate ? Yii::$app->getFormatter()->asDate($this->Birthdate, 'php:Y-m-d') : $this->Birthdate;
            $this->_defineExpirationDatePass();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        
        $this->refresh();
        $this->_getAssignedRole();
        if(!$this->_verifyRole()){
            $this->_revokeProfile();
        }
        if(!$this->_role){
            $this->_assignProfile();
        }
        if($this->_customPassword && !$this->_new){
            $this->updateOperator ? $this->updatePasswordToOperator():NULL;
            Yii::$app->getSession()->setFlash('success','Contraseña de Usuario Actualizada');
        } elseif($this->_new) {
            Yii::$app->getSession()->setFlash('success','Usuario Creado Correctamente');
        } else {
            Yii::$app->getSession()->setFlash('success','Usuario Actualizado Correctamente');
        }

        $useroptions = new Useroption();
        if($this->usersetting){
            $settings = $this->usersetting;
            $useroptions->IdUser = $this->Id;
            $useroptions->permissions = $settings['Custom'];
            unset($settings["Custom"]);
            $useroptions->enabledOptions = $settings;
            $useroptions->_setPermissions();
        } elseif($this->_emptyUserOptions){
            $useroptions->IdUser = $this->Id;
            $useroptions->_resetAllPermissions();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getCompaniesuser(){
        return $this->hasMany(Companyuser::class, ['IdUser' => 'Id']);
    }
    
    public function setCompany($company){
        $this->_session = Yii::$app->getSession();
        $this->_session->set('user.company', $company);
        $this->company = $company;
    }

    public function getCompany(){
        $this->company = Yii::$app->getSession()->get('user.company');
        return $this->company;
    }
    
    public function afterFind() {
        try {
            if($this->scenario != self::SCENARIO_CONSOLE){
                $this->profileName = $this->IdProfile ? $this->profile->Name:"";
                $this->completeName = $this->FirstName." ".$this->LastName;

                $this->stateName = $this->IdState ? $this->state->Name:"";
                $this->disabled = ( !$this->isNewRecord ? ( $this->IdState ? ($this->state->Code == self::STATE_INACTIVE):FALSE ): FALSE);
            }
            $this->CreateDate = $this->CreateDate ? \Yii::$app->formatter->asDate($this->CreateDate,'php:d-m-Y H:i:s') : $this->CreateDate;
            $this->UpdateDate = $this->UpdateDate ? \Yii::$app->formatter->asDate($this->UpdateDate,'php:d-m-Y H:i:s') : $this->UpdateDate;
            $this->Birthdate = $this->Birthdate ? \Yii::$app->formatter->asDate($this->Birthdate,'php:d-m-Y') : $this->Birthdate;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    private function _getAssignedRole(){
        try {
            $this->_role = $this->auth->getUserAssignments($this->Id);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function setExpirationDate(){
        try{
            $date = date_create(date('Y-m-d'));
            $dexp = $this->PasswordExpirationDate ? \DateTime::createFromFormat('Y-m-d', $this->PasswordExpirationDate): date_sub($date, date_interval_create_from_date_string("1 day"));
            $diff = $date->diff($dexp);

            if($diff->days == 0 || ($diff->invert == 1 && $diff->days > 0)){
                $this->expired = true;
                $this->remainingDays = $diff->days;
            } else {
                $this->expired = false;
                $this->remainingDays = $diff->days;
            }
            
            if($this->expired == false){
                $warningDays = Settingdetail::find()
                        ->select([Settingdetail::tableName().'.Id',Settingdetail::tableName().'.Value',Settingdetail::tableName().'.IdSetting'])
                        ->joinWith('setting b')
                        ->where(['b.KeyWord'=>'User','b.Code'=> self::PASSWORD_EXPIRATION_WARNING_PARAMETER, Settingdetail::tableName().'.Code'=> self::PASSWORD_EXPIRATION_WARNING_PARAMETER])
                        ->one();
                if($warningDays != null){
                    $this->warningPass = (int) $warningDays->Value >= $diff->days;
                }
            }
            $this->PasswordExpirationDate = $this->PasswordExpirationDate ? \Yii::$app->formatter->asDate($this->PasswordExpirationDate, 'php:d-m-Y'):$this->PasswordExpirationDate;
            
        } catch (Exception $exc){
            throw $exc;
        }
    }
    
    private function _verifyRole(){
        try {
            if($this->IdProfile){
                $rolename = $this->profile->KeyWord;
                $role = $this->auth->getRole($rolename);
                return $role ? in_array($role->name,$this->_role):TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _assignProfile(){
        try {
            $this->auth->assignRole($this->Id, $this->profile->KeyWord);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _revokeProfile(){
        try {
            $this->auth->revokeAllRoles($this->Id);
            $this->_role = NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function comparePasswords($password){
        try {
            return \Yii::$app->security->validatePassword($password, $this->PasswordHash);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function updateRamdomPass(){
        try {
            $this->_password = Yii::$app->security->generateRandomString(12);
            $this->_defineExpirationDatePass();
            $this->save();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _defineExpirationDatePass(){
        try {
            if($this->_password){
                $this->setPassword($this->_password);
                $this->_customPassword = true;
                $this->_new = $this->isNewRecord;

                $user = \Yii::$app->user->getIdentity();
                if(($this->isNewRecord || $this->Username != $user->Username) && $this->scenario != self::SCENARIO_WEBSERVICE){
                    $date = date_sub(date_create(date('Y-m-d')), date_interval_create_from_date_string("1 day"));
                } else {
                    $days = $this->_getExpirationPassSetting();
                    $date = date_add(date_create(date('Y-m-d')), date_interval_create_from_date_string("$days day"));
                    
                }
                $this->PasswordExpirationDate = $date->format('Y-m-d');
            } else {
                $this->PasswordExpirationDate = $this->PasswordExpirationDate ? Yii::$app->getFormatter()->asDate($this->PasswordExpirationDate,'php:Y-m-d'): $this->PasswordExpirationDate;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getExpirationPassSetting(){
        try {
            $daysSetting = Settingdetail::find()
                        ->select([Settingdetail::tableName().'.Id',Settingdetail::tableName().'.Value',Settingdetail::tableName().'.IdSetting',Settingdetail::tableName().'.IdState'])
                        ->joinWith('setting b')
                        ->joinWith('state c')
                        ->where([Settingdetail::tableName().'.Code'=> self::PASSWORD_EXPIRATION_PARAMETER
                                , 'b.Code'=> self::PASSWORD_EXPIRATION_PARAMETER
                                , 'c.Code'=> Setting::STATUS_ACTIVE,
                            ])
                        ->asArray()
                        ->one();
                $days = !empty($daysSetting) ? (int)$daysSetting['Value']:self::DEFAULT_PASS_EXPIRATION_DAYS;
                return $days;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getOperator(){
        #$this->operator = $this->syncPass ? Tbloperator::find()->where(['CODOPER'=> $this->Username])->one() : null;
    }
    
    public function getOperator(){
        return $this->operator;
    }
    public function setOperator(){
        try {
            $this->_getOperator();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function updatePasswordFromOperator(){
        try {
            $this->setOperator();
            if(!empty($this->operator)){
                $this->operator->password = $this->_password;
                if($this->operator->comparePass()){
                    if(!$this->comparePasswords($this->_password)){
                        $this->setPassword($this->password);
                        $this->save();
                        $this->refresh();
                    }
                } else {
                    return TRUE;
                }
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function validateOperator($password){
        try {
            $this->_getOperator();
            if($this->operator){
                $this->operator->password = $password;
                if($this->operator->comparePass()){
                    if(!$this->comparePasswords($password)){
                        $this->setPassword($password);
                        $this->save();
                        $this->refresh();
                    }
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function updatePasswordToOperator(){
        try {
            $this->_getOperator();
            if($this->operator){
                $this->operator->password = $this->_password;
                $this->operator->EXPIREDATEPASS = $this->PasswordExpirationDate;
                $this->operator->updatePassword();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _generateTempToken(){
        try {
            $this->TempToken = Yii::$app->security->generateRandomString(24);
            $this->save();
            $this->refresh();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function sendAccessMail(){
        try {
            $this->_generateTempToken();
            $url = Yii::$app->urlManager->createAbsoluteUrl(['site/logon','token'=> $this->TempToken]);
            $urlSite = Url::to(\Yii::$app->params['mainSiteUrl']['url']);
            
            $link = "<a href='$url'>ACCESO</a>";
            $body = '<ul> '
                    . '<li><b>'.$this->DisplayName.'</b></li>'
                    . '<li>Código Empleado: <b>'.$this->CodEmployee.'</b></li>'
                    . '</ul>'
                    . '<b>Enlace para Confirmar Acuerdo:<br/>'
                    . $link;
            $footer = "<br/>"
                    . "<b>*El Enlace es Válido sólo para un Acceso</b><br/>"
                    . "<b>**Para volver a accesar deberá generar un nuevo enlace</b><br/>"
                    . "<br/>"
                    . "<b>Visite ".$urlSite." para más obtener un nuevo acceso<br/>"
                    ;
            $content = [
                'title'=>'Verificación de Empleado Muhlbauer El Salvador',
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
                ->setTo($this->Email)
                ->setSubject($content['title'])
                ->send();
            
            if($email){
                #Yii::$app->getSession()->setFlash('success','Revisa la Bandeja de tu Email!');
                return true;
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}