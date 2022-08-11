<?php

namespace backend\models;

use common\models\Type;
use backend\models\Transactionmodel;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "transaction".
 *
 * @property string $Id
 * @property string $TransactionKey
 * @property int $IdTransactionModel
 * @property int $IdTransaction
 * @property string $ActionType
 * @property string $IdTransactionBatch
 * @property string $CreationDate
 *
 * @property Transactionbatch $transactionBatch
 * @property Transactionmodel $transactionModel
 * @property Type $type
 * @property Transactiondetail[] $transactiondetails
 */
class Transaction extends \yii\db\ActiveRecord
{
    
    const TYPE_NEWRECORD = 'NEW';
    const TYPE_UPDATE = 'UPD';
    const TYPE_DELETE = 'DEL';
    const TYPE_GET = 'GET';
    const TYPE_LIST = 'LIST';
    const TYPE_DEFAULT = 'DFLT';
    
    private $actionTypes = [];
    
    public $details;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTransactionModel', 'ActionType', 'IdTransactionBatch','IdTransaction'], 'required'],
            [['IdTransactionModel', 'IdTransactionBatch','IdTransaction'], 'integer'],
            [['CreationDate'], 'safe'],
            [['TransactionKey'], 'string', 'max' => 300],
            [['IdTransactionBatch'], 'exist', 'skipOnError' => true, 'targetClass' => Transactionbatch::className(), 'targetAttribute' => ['IdTransactionBatch' => 'Id']],
            [['IdTransactionModel'], 'exist', 'skipOnError' => true, 'targetClass' => Transactionmodel::className(), 'targetAttribute' => ['IdTransactionModel' => 'Id']],
            [['ActionType'], 'string', 'max' => 4],
            [['ActionType'],'in','range'=>[self::TYPE_DEFAULT, self::TYPE_NEWRECORD, self::TYPE_UPDATE, self::TYPE_DELETE, self::TYPE_GET, self::TYPE_LIST]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'TransactionKey' => 'Transaction Key',
            'IdTransaction' => 'Id de Transacción',
            'IdTransactionModel' => 'Model de Transacción',
            'ActionType' => 'Tipo de Transacción',
            'IdTransactionBatch' => 'Id Lote',
            'CreationDate' => 'Fecha de Creación',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionBatch()
    {
        return $this->hasOne(Transactionbatch::className(), ['Id' => 'IdTransactionBatch']);
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
    public function getTransactiondetails()
    {
        return $this->hasMany(Transactiondetail::className(), ['IdTransaction' => 'Id']);
    }
    
    public function setType($type = 'DFLT'){
        try {
            $this->ActionType = $type ? $type:self::TYPE_DEFAULT;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function setActionTypes(){
        $this->actionTypes = [
            ['Id' => self::TYPE_DEFAULT, 'Name' => 'Acción por Defecto'],
            ['Id' => self::TYPE_NEWRECORD, 'Name' => 'Nuevo Registro'],
            ['Id' => self::TYPE_UPDATE, 'Name' => 'Actualización'],
            ['Id' => self::TYPE_DELETE, 'Name' => 'Eliminación'],
            ['Id' => self::TYPE_GET, 'Name' => 'Obtener Registro'],
            ['Id' => self::TYPE_LIST, 'Name' => 'Listar Registros'],
        ];
    }
    
    public function getActionTypes(){
        $this->setActionTypes();
        return ArrayHelper::map($this->actionTypes, 'Id', 'Name');
    }

    public function beforeSave($insert) {
        try {
            if($this->CreationDate){
                $this->CreationDate = Yii::$app->formatter->asDatetime($this->CreationDate, 'php:Y-m-d H:i:s');
            } else {
                $this->CreationDate = Yii::$app->formatter->asDatetime(date('Y-m-d H:i:s'), 'php:Y-m-d H:i:s');
            }
            if(empty($this->TransactionKey)){
                $this->TransactionKey = Yii::$app->getSecurity()->generateRandomString(32);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function setTransactionModel($tmodel = NULL){
        try {
            if($tmodel){
                $model = Transactionmodel::findOne(['ModelName'=>$tmodel->ModelName, 'NameSpace'=> $tmodel->NameSpace]);
                if($model){
                    return $model;
                } else {
                    return $this->_saveTransactionModel($tmodel);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveTransactionModel($tmodel = NULL){
        try {
            if($tmodel){
                if(!$tmodel->save()){
                    $message = Yii::$app->customFunctions->getErrors($tmodel->errors);
                    throw new Exception('ERROR: '.$message, 92002);
                } else {
                    return $tmodel;
                }
            } else {
                throw new Exception('No se encontró modelo para transacción', 91001);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function setDetail($detail = []){
        try {
            foreach ($detail as $key => $value ){
                $det = new Transactiondetail();
                $det->IdTransaction = $this->Id;
                $det->Attribute = $key;
                $det->Value = $value;
                if($det->save()){
                    
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
