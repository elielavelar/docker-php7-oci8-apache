<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use common\models\State;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;
use yii\helpers\Url;

use backend\models\Settingdetail;
/**
 * This is the model class for table "citizen".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $LastName
 * @property string $Email
 * @property string $Telephone
 * @property string $PasswordHash
 * @property string $PasswordResetToken
 * @property string $AuthKey
 * @property string $CreateDate
 * @property string $UpdateDate
 * @property string $SignUpMethod
 * @property string $ShortCode
 * @property integer $IdState
 *
 * @property State $idState
 */
class Citizen extends \yii\db\ActiveRecord implements IdentityInterface
{
    
    public $view;
    public $create;
    public $update;
    public $delete;


    const STATUS_DELETED = 'CER';
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    public $username;
    public $CompleteName;
    
    const SHORT_CODE = 'SHCOD';
    
    const SCENARIO_SIGNUP = 'signup';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_ADMIN = 'admin';
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SIGNUP] = ['Id','Name','LastName','Email','PasswordHash','AuthKey','IdState','SignUpMethod','ShortCode'];
        $scenarios[self::SCENARIO_CREATE] = ['Id','Name','LastName','Telephone','Email','PasswordHash','AuthKey','IdState','SignUpMethod','ShortCode'];
        $scenarios[self::SCENARIO_ADMIN] = ['Id','Name','LastName','Telephone','Email','PasswordHash','AuthKey','IdState','SignUpMethod','ShortCode'];
        
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%citizen}}';
    }
    
    public static function getTableName(){
        return 'citizen';
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
            [['Name', 'LastName', 'Email', 'PasswordHash', 'AuthKey'], 'required','on'=>'signup','message'=>'{attribute} no puede quedar vacío'],
            [['Name', 'LastName'], 'required','on'=>'create','message'=>'{attribute} no puede quedar vacío'],
            [['Email'], 'unique','on'=>'default'], 
            [['Email'], 'unique','on'=>'signup'], 
            [['Email'], 'email'], 
            [['CreateDate', 'UpdateDate'], 'safe'],
            [['IdState'], 'integer'],
            [['IdState'], 'default','value'=>  State::findOne(['KeyWord'=>'Citizen','Code'=>  self::STATUS_INACTIVE])->Id],
            [['AuthKey'],'default','value'=> $this->generateAuthKey(),'on'=>'create'],
            [['PasswordHash'],'default','value'=> function($attribute, $params){
                $this->setPassword(\Yii::$app->customFunctions->getRandomPass());
                return $this->PasswordHash;
            },'on'=>'create'],
            [['Name', 'Email'], 'string', 'max' => 50],
            [['LastName'], 'string', 'max' => 65],
            [['Telephone'], 'string', 'max' => 12],
            [['SignUpMethod'], 'string', 'max' => 50],
            [['ShortCode'], 'string', 'max' => 8],
            [['PasswordHash', 'PasswordResetToken', 'AuthKey'], 'string', 'max' => 100],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombres',
            'LastName' => 'Apellidos',
            'Email' => 'Email',
            'Telephone' => 'Teléfono',
            'PasswordHash' => 'Password Hash',
            'PasswordResetToken' => 'Password Reset Token',
            'AuthKey' => 'Auth Key',
            'CreateDate' => 'Fecha Creación Usuario',
            'UpdateDate' => 'Última Actualización',
            'IdState' => 'Estado',
            'SignUpMethod'=>'Método Registro',
            'ShortCode'=>'Código Corto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdState()
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
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        #return static::findOne(['id' => $id, 'idstate' => State::findOne(['KeyWord'=>'Citizen','Code'=>self::STATUS_ACTIVE])]);
        return static::findOne(['id' => $id]);
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
    public static function findIdentityByAuthKey($auth_key){
        return static::findOne(['authkey'=>$auth_key]);
    }
    
    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        #return static::findOne(['email' => $email, 'idstate' => State::findOne(['KeyWord'=>'Citizen','Code'=>self::STATUS_ACTIVE])]);
        return static::findOne(['email' => $email]);
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
            'passwordresettoken' => $token,
            #'idstate' => State::findOne(['KeyWord'=>'Citizen','Code'=>self::STATUS_ACTIVE]),
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
        $expire = Yii::$app->params['citizen.passwordResetTokenExpire'];
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
        if($this->isNewRecord){
            if($this->scenario == self::SCENARIO_CREATE){
                $this->generateAuthKey();
            }
            $this->_generateShortCode();
        } elseif (empty ($this->ShortCode)) {
            $this->_generateShortCode();
        }
        
        if($this->scenario == self::SCENARIO_CREATE){
            if(!$this->validateSignup(NULL, NULL)){
                return FALSE;
            }
        }
        
        $this->Name = strtoupper($this->Name);
        $this->LastName = strtoupper($this->LastName);
     
        $this->SignUpMethod = StringHelper::basename(Yii::getAlias('@app'));
        
        return parent::beforeSave($insert);
    }
    
    public function afterFind() {
        
        $this->create = \Yii::$app->user->can('citizenCreate');
        $this->update = \Yii::$app->user->can('citizenUpdate');
        $this->delete = \Yii::$app->user->can('citizenDelete');
        $this->view = \Yii::$app->user->can('citizenView');
        
        $this->username = $this->Email;
        $this->CompleteName = $this->Name." ".$this->LastName;
        return parent::afterFind();
    }
    
    public function sendEmailConfirmation(){
        try {
            if(empty($this->ShortCode)){
                $this->generateShortCode();
            }
            $app = StringHelper::basename(Yii::getAlias('@app')); 
            if($app == 'backend'){
                $url = Url::to('http://citas.'.\Yii::$app->params['mainSiteUrl']['name']."/site/confirm/".$this->Id.'?key='.$this->AuthKey);
            } else{
                $url = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm','id'=>$this->Id,'key'=>$this->AuthKey]);
            }
            $link = "<a href='$url'>Confirmar</a>";
            
            $body = '<h4>Gracias por Crear su Usuario en el sistema de Citas.<br/>Confirme su usuario siguiendo los pasos a continuación para registrar una cita</h4>'
                    . '<ul> '
                    . '<li>Nombres: <b>'.$this->Name.'</b></li>'
                    . '<li>Apellidos: <b>'.$this->LastName.'</b></li>'
                    . '<li>Código de Confirmación:<br/>'
                    . '<h2>'.$this->ShortCode.'</h2>'
                    . '</li>'
                    . '</ul>'
                    . "<b>D&eacute; Click sobre este enlace para confirmar su Usuario:</b> ".$link;
            $body .= "<br/>"
                    . "<h4>Pasos Siguientes</h4>"
                    . "<ol>"
                    . "<li>Ingrese el código de Confirmación en el formulario del perfil de usuario en el sistema o dé click en el link: <h4>$link</h4></li>"
                    . "<li>Después de confirmar su usuario, se mostrará el formulario de creación de cita</li>"
                    . "<li>Seleccione Duicentro, Tipo de Trámite, Fecha y Hora preferidas</li>"
                    . "<li>De click en el bot&oacute;n Registrar</li>"
                    . "<li>Recibirá un Correo Electrónico con su Código de Confirmación</li>"
                    . "</ol>";
            
            $content = [
                'title'=>'Confirmación de Creación de Usuario',
                'body'=>$body,
                'footer'=>'',
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
                #->setHtmlBody($content['body'])
                ->send();
            
            if($email){
                Yii::$app->getSession()->setFlash('success','Revisa la Bandeja de tu Email para confirmar el registro y activar usuario. Luego inicia sesión y programa una cita');
            } else{
                Yii::$app->getSession()->setFlash('warning','Error, contacte al Administrador!');
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function validateSignup($attribute, $params){
        try {
            if(empty($this->Email) && empty($this->Telephone)){
                #$message = "Al menos uno de los valores es requerido";
                #$this->addError('Email', $message);
                #$this->addError('Telephone', $message);
                #return FALSE;
                return TRUE;
            } else {
                return TRUE;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _generateShortCode(){
        try {
            $length = $this->_getLengthCode();
            $this->ShortCode = \Yii::$app->customFunctions->getRandomString($length, FALSE, 2);
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function generateShortCode(){
        try {
            $this->_generateShortCode();
            $this->save();
            $this->refresh();
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    private function _getLengthCode(){
        try {
            $lenght = 4;
            $value = Settingdetail::find()->where([Settingdetail::tableName().'.Code'=> self::SHORT_CODE])
                    ->joinWith('setting b',true)
                    ->andWhere(['b.KeyWord'=> StringHelper::basename(self::class),'b.Code'=> self::SHORT_CODE])
                    ->one();
            if($value != NULL){
                $lenght = $value->Value;
            }
            return $lenght;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function activate(){
        try {
            $this->IdState = State::findOne(['KeyWord'=> StringHelper::basename(self::class),'Code'=> self::STATUS_ACTIVE])->Id;
            $this->save();
            $this->refresh();
            if($this->idState->Code == self::STATUS_ACTIVE){
                return [
                    'success'=> TRUE,
                    'message'=> 'Usuario Activado Correctamente'
                ];
            } else {
                throw new Exception('Error al activar el Usuario', 92000);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
