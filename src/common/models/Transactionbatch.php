<?php

namespace common\models;
use yii\helpers\StringHelper;
use backend\components\XMLFormatter;
use backend\components\XMLResponse;
use Exception;
use SimpleXMLElement;
use common\models\Registredmodels;
use common\models\Transactionmodel;
use common\models\Transaction;
use common\models\Transactiondetail;

use Yii;

/**
 * This is the model class for table "transactionbatch".
 *
 * @property string $Id
 * @property string $BatchKey
 * @property string $ShortCode
 * @property int $Enabled
 * @property int $Applied
 * @property string $CreationDate
 * @property string $CloseDate 
 *
 * @property Transaction[] $transactions
 */
class Transactionbatch extends \yii\db\ActiveRecord
{
    const SHORTCODE_LENGTH = 8;
    const SCENARIO_UPLOAD = 'upload';
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    const STATUS_APPLIED = 1;
    const STATUS_UNAPPLIED = 0;
    
    public $uploadFile = NULL;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactionbatch';
    }

    
     public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPLOAD] = ['BatchKey','ShortCode','Enabled','Applied','CreationDate','CloseDate'];
        
        return $scenarios;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Enabled','Applied'], 'integer'],
            [['Enabled'], 'default','value'=> self::STATUS_ACTIVE],
            [['Enabled'],'in','range'=>[self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['Applied'], 'default','value'=> self::STATUS_UNAPPLIED],
            [['Applied'],'in','range'=>[self::STATUS_APPLIED, self::STATUS_UNAPPLIED]],
            [['CreationDate','CloseDate'], 'safe'],
            [['BatchKey'], 'string', 'max' => 300],
            [['ShortCode'], 'string', 'max' => 8],
            [['uploadFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xml','on'=>'upload'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'Id',
            'BatchKey' => 'Id Batch',
            'Enabled' => 'Habilitado',
            'Applied' => 'Aplicado',
            'CreationDate' => 'Fecha de Creación',
            'CloseDate' => 'Fecha de Cierre',
            'uploadFile'=>'Archivo de Carga',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['IdTransactionBatch' => 'Id']);
    }

    
    private function _generateShortCode(){
        try {
            #$this->ShortCode = \Yii::$app->customFunctions->getRandomString(self::SHORTCODE_LENGTH, FALSE, 2);
            $this->ShortCode = Yii::$app->security->generateRandomString(self::SHORTCODE_LENGTH);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getActiveBatch(){
        try {
            return self::find()
                    ->where(['Enabled'=> self::STATUS_ACTIVE])
                    ->one();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function createBatch(){
        try {
            $model = new Transactionbatch();
            $model->Applied = self::STATUS_APPLIED;
            
            $model->save();
            $model->refresh();
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function closeBatch(){
        try {
            $this->Enabled = self::STATUS_INACTIVE;
            $this->CloseDate = date('d-m-Y H:i:s');
            if(!$this->save()){
                $message = Yii::$app->customFunctions->getErrors($this->errors);
                throw new Exception($message, 92001);
            }
            $this->refresh();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function loadBatch(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function applyBatch(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        try {
            if($this->CreationDate){
                $this->CreationDate = Yii::$app->formatter->asDatetime($this->CreationDate, 'php:Y-m-d H:i:s');
            } else {
                $this->CreationDate = Yii::$app->formatter->asDatetime(date('Y-m-d H:i:s'), 'php:Y-m-d H:i:s');
            }
            if($this->CloseDate){
                $this->CloseDate = Yii::$app->formatter->asDatetime($this->CloseDate, 'php:Y-m-d H:i:s');
            }
            if(empty($this->BatchKey)){
                $this->BatchKey = Yii::$app->getSecurity()->generateRandomString(32);
            }
            
            if(empty($this->ShortCode)){
                $this->_generateShortCode();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->CreationDate = Yii::$app->formatter->asDatetime($this->CreationDate, 'php:d-m-Y H:i:s');
            $this->CloseDate = Yii::$app->formatter->asDatetime($this->CloseDate, 'php:d-m-Y H:i:s');
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterFind() {
        try {
            $this->CreationDate = Yii::$app->formatter->asDatetime($this->CreationDate, 'php:d-m-Y H:i:s');
            $this->CloseDate = $this->CloseDate ? Yii::$app->formatter->asDatetime($this->CloseDate, 'php:d-m-Y H:i:s'):$this->CloseDate;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function generateXMLFile($saveFile = TRUE){
        try {
            $transactionbatch = $this->attributes;
            $transactions = $this->transactions;
            $_transactions = [];
            $registredmodels = [];
            $_registredmodels = [];
            $transactionmodels = [];
            $_transactionmodels = [];
            foreach ($transactions as $t){
                $transaction = $t->attributes;
                $_type = NULL;
                switch ($t->ActionType){
                    case Transaction::TYPE_UPDATE:
                        $_type = 'UpdateTransaction';
                        break;
                    case Transaction::TYPE_DELETE: 
                        $_type = 'DeleteTransaction';
                        break;
                    case Transaction::TYPE_NEWRECORD:
                    default :
                        $_type = 'CreateTransaction';
                        break;
                }
                $transaction['Transactiondetails'] = $t->transactiondetails;
                
                $_transactions[$_type][][StringHelper::basename(Transaction::class)] = $transaction;
                $_transactionmodels[$t->transactionModel->registredModel->KeyWord] = [StringHelper::basename(Transactionmodel::class)=>$t->transactionModel->attributes];
                $_registredmodels[$t->transactionModel->registredModel->KeyWord] = [StringHelper::basename(Registredmodels::class) => $t->transactionModel->registredModel->attributes];
            }
            $transactionbatch['Transactions'] = $_transactions;
            foreach ($_transactionmodels as $_t){
                $transactionmodels[] = $_t;
                #array_push($transactionmodels, $_t);
            }
            foreach ($_registredmodels as $_t){
                $registredmodels[] = $_t;
                #array_push($registredmodels, $_t);
            }
            $batch = [];
            $batch["RegistredModels"]= $registredmodels;
            $batch["TransactionModels"]= $transactionmodels;
            $batch["TransactionBatches"][][StringHelper::basename(self::class)] = $transactionbatch;
            return $this->_buildXML($batch, $saveFile);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _buildXML($data = [], $saveFile = TRUE){
        try {
            $xmlResponse = new XMLResponse();
            $xmlResponse->setCharset('UTF-8');
            $xmlResponse->setIncludeStandalone(true);
            $xmlResponse->data = $data;
            $xmlResponse->appendAttribute('Id');
            $formatter = new XMLFormatter();
            $formatter->format($xmlResponse);
            return $saveFile ? $this->_saveFile($xmlResponse):$xmlResponse->content;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveFile(XMLResponse $xmlFile){
        try {
            $tempName = StringHelper::basename(self::class)."_".$this->ShortCode.".xml";
            $tempFile =  \Yii::getAlias("@backend/web/temp/$tempName");
            $xmlFile->dom->formatOutput = true;
            if(file_exists($tempName)){
                unlink($tempName);
            }
            $xmlFile->dom->save($tempFile);
            $url = \yii\helpers\Url::to("@web/temp/$tempName");
            return $url;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function upload(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $pFile = $this->uploadFile->tempName;
            $xml = new SimpleXMLElement($pFile, 0, true);
            $this->_iterateXMLElement($xml);
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        } 
    }
    
    private function _iterateXMLElement(SimpleXMLElement $xml){
        try {
            $count = 0;
            foreach ($xml as $key => $node) {
                switch (gettype($key)){
                    case 'string':
                        switch ($key){
                            case 'item':
                                break;
                            case StringHelper::basename(Registredmodels::class):
                                break;
                            case StringHelper::basename(Transactionmodel::class):
                                break;
                            case StringHelper::basename(Transactionbatch::class):
                                break;
                            case StringHelper::basename(Transaction::class):
                                break;
                            case StringHelper::basename(Transactiondetail::class):
                                break;
                        }
                        $key = NULL;
                        foreach ($node->attributes() as  $att){
                            $key = (array) $att;
                        }

                        $_node = (string) $node;
                        #$elements[] = ["ID"=> $count,"KEY"=>$key[0],"NAME"=>$_node];
                        $count++;
                        break;
                    default :
                        $this->_iterateXMLElement($node);
                        break;
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateTransactionModel($xml){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public static function getBatch(){
        try {
            $model = Transactionbatch::find()->where(['Enabled' => self::STATUS_ACTIVE])->one();
            if($model != null){
                $response = $model->generateXMLFile(false);
                $model->closeBatch();
                return $response;
            } else {
                throw new Exception('Lote no encontrado',90000);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
