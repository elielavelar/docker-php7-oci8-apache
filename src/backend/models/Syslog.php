<?php

namespace backend\models;

use Yii;
use common\models\User;
use backend\models\Transactionmodel;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "syslog".
 *
 * @property int $Id
 * @property string $LogKey
 * @property int $IdTransactionModel
 * @property string $Title
 * @property int $IdRecord
 * @property string $ActionType
 * @property string $CreationDate
 * @property int $IdUser
 * @property string $ControllerName
 * @property string $ActionName
 * @property string $EnvironmentName
 * @property string $Description
 *
 * @property Transactionmodel $transactionModel
 * @property User $user
 * @property Syslogdetail[] $syslogdetails
 */
class Syslog extends \yii\db\ActiveRecord
{
    
    const TYPE_NEWRECORD = 'NEW';
    const TYPE_UPDATE = 'UPD';
    const TYPE_DELETE = 'DEL';
    const TYPE_GET = 'GET';
    const TYPE_LIST = 'LIST';
    const TYPE_DEFAULT = 'DFLT';
    
    private $actionTypes = [];
    private $actionType = [];
    
    public $actionTypeName = NULL;
    public $transactionModelName = NULL;
    public $userName = NULL;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syslog';
    }
    
    public function __construct($config = array()) {
        try {
            $this->actionType = [
                self::TYPE_DEFAULT => 'Acción por Defecto',
                self::TYPE_NEWRECORD => 'Nuevo Registro',
                self::TYPE_UPDATE => 'Actualización',
                self::TYPE_DELETE => 'Eliminación',
                self::TYPE_GET => 'Obtener Registro',
                self::TYPE_LIST => 'Listar Registros',
            ];
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTransactionModel', 'Title', 'IdRecord', 'IdUser', 'ControllerName', 'ActionName', 'EnvironmentName'], 'required'],
            [['IdTransactionModel', 'IdRecord', 'IdUser'], 'integer'],
            [['CreationDate'], 'safe'],
            [['Description'], 'string'],
            [['LogKey'], 'string', 'max' => 300],
            [['Title'], 'string', 'max' => 150],
            [['ActionType'], 'string', 'max' => 4],
            [['ControllerName', 'ActionName'], 'string', 'max' => 100],
            [['EnvironmentName'], 'string', 'max' => 50],
            [['IdTransactionModel'], 'exist', 'skipOnError' => true, 'targetClass' => Transactionmodel::className(), 'targetAttribute' => ['IdTransactionModel' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'LogKey' => 'Llave de Log',
            'IdTransactionModel' => 'Modelo Transacción',
            'Title' => 'Título',
            'IdRecord' => 'Id de Registro',
            'ActionType' => 'Tipo de Acción',
            'CreationDate' => 'Fecha Creación',
            'IdUser' => 'Usuario',
            'ControllerName' => 'Controlador',
            'ActionName' => 'Acción',
            'EnvironmentName' => 'Entorno',
            'Description' => 'Descripción',
            'transactionModelName' => 'Modelo de Transacción',
            'userName' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionModel()
    {
        return $this->hasOne(Transactionmodel::className(), ['Id' => 'IdTransactionModel']);
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
    public function getSyslogdetails()
    {
        return $this->hasMany(Syslogdetail::className(), ['IdSysLog' => 'Id']);
    }
    
    private function setActionTypes(){
        foreach ($this->actionType as $key => $value){
            $this->actionTypes[] = ['Id' => $key, 'Name' => $value];
        }
    }
    
    public function getActionType($type = 'DFLT'){
        return isset($this->actionType[$type]) ? $this->actionType[$type]:$this->actionType['DFLT'];
    }
    
    public function getActionTypes(){
        $this->setActionTypes();
        return ArrayHelper::map($this->actionTypes, 'Id', 'Name');
    }
    
    public function afterFind() {
        $this->transactionModelName = $this->IdTransactionModel ? $this->transactionModel->ModelName: $this->transactionModelName;
        $this->actionTypeName = $this->getActionType($this->ActionType);
        $this->userName = $this->IdUser ? $this->user->DisplayName:$this->userName;
        $this->CreationDate = Yii::$app->formatter->asDatetime($this->CreationDate, 'php:d-m-Y H:i:s');
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        if(empty($this->LogKey)){
            $this->LogKey = Yii::$app->getSecurity()->generateRandomString(32);
        }
        return parent::beforeSave($insert);
    }


    public function setDetail($detail = []){
        try {
            foreach ($detail as $key => $values ){
                $det = new Syslogdetail();
                $det->IdSysLog = $this->Id;
                $det->Attribute = $key;
                $det->Value = $values['VALUE'];
                $det->OldValue = $values['OLDVALUE'];
                if($det->save()){
                    
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
