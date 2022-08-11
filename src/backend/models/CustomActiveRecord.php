<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\models;
use Yii;
use yii\db\ActiveRecord;
use backend\models\Transaction;
use backend\models\Transactionbatch;
use backend\models\Transactionmodel;
use backend\models\Transactiondetail;
use backend\models\Syslog;
use backend\models\Syslogdetail;
use common\models\Type;
use yii\helpers\StringHelper;
use Exception;
use backend\models\Option;

/**
 * Description of CustomActiveRecord
 *
 * @author avelare
 */
class CustomActiveRecord extends ActiveRecord {
    private $batch = NULL;
    private $transaction;
    private $syslog;
    
    private $dbTransaction;
    private $tmodel;
    public $namespace = NULL;
    public $classname = NULL;
    private $_oldAttributes = [];
    private $_newRecord = TRUE;
    private $_pk = NULL;
    
    private $saveTransaction = FALSE;
    private $saveLog = FALSE;
    private $_controller = NULL;
    private $_action = NULL;
    private $_actionKey = NULL;
    
    public function __construct($config = array()) {
        try {
            $this->_controller = \Yii::$app->controller->id;
            $this->_action = \Yii::$app->controller->action->id;
            $this->_actionKey = $this->_controller. ucfirst($this->_action);
            $this->getSettingOption();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::__construct($config);
    }

    public function setSaveTransaction($setting = FALSE) {
        $this->saveTransaction = $setting;
    }

    public function setSaveLog($setting = FALSE) {
        $this->saveLog = $setting;
    }

    private function getSettingOption(){
        try {
            $option = Option::findOne(['KeyWord'=> $this->_actionKey]);
            if($option){
                $this->saveTransaction = $option->SaveTransaction == 1;
                $this->saveLog = $option->SaveLog == 1;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        try {
            $this->_newRecord = $this->isNewRecord;
            $this->_oldAttributes = $this->oldAttributes;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try{
            if($this->saveTransaction){
                $this->_setTransaction();
            }
            if($this->saveLog){
                $this->_saveLog();
            }
        } catch (Exception $ex){
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function beforeDelete() {
        try {
            $this->_pk = $this->getPrimaryKey();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeDelete();
    }

    public function afterDelete() {
        try {
            if($this->saveTransaction){
                $this->_setDeleteTransaction();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterDelete();
    }

    private function _setTransaction(){
        $this->dbTransaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $attributes = $this->_newRecord ? $this->attributes : $attributes = \Yii::$app->customFunctions->getAttributeChanges($this->_oldAttributes, $this->attributes);
            if(!empty($attributes)){
                $this->_setTransactionModel();
                if($this->tmodel->Enabled == Transactionmodel::STATUS_ACTIVE){
                    $this->_setBatch();
                    $this->transaction = new Transaction();
                    $this->transaction->IdTransactionBatch = $this->batch->Id;
                    $this->transaction->ActionType = ($this->_newRecord ? Transaction::TYPE_NEWRECORD:Transaction::TYPE_UPDATE);
                    $this->transaction->IdTransactionModel = $this->tmodel->Id;
                    $this->transaction->IdTransaction = $this->getPrimaryKey();
                    if($this->transaction->save()){
                        $this->transaction->setDetail($attributes);
                    } else {
                        $message = \Yii::$app->customFunctions->getErrors($this->transaction->errors);
                        throw new Exception($message, 90002);
                    }
                }
            }
            $this->dbTransaction->commit();
        } catch (Exception $ex) {
            $this->dbTransaction->rollback();
            throw $ex;
        }
    }
    
    private function _setDeleteTransaction(){
        $this->dbTransaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->_setTransactionModel();
            if($this->tmodel->Enabled == Transactionmodel::STATUS_ACTIVE){
                $this->_setBatch();
                $this->transaction = new Transaction();
                $this->transaction->IdTransactionBatch = $this->batch->Id;
                $this->transaction->ActionType = Transaction::TYPE_DELETE;
                $this->transaction->IdTransactionModel = $this->tmodel->Id;
                $this->transaction->IdTransaction = $this->_pk;
                if(!$this->transaction->save()){
                    $message = \Yii::$app->customFunctions->getErrors($this->transaction->errors);
                    throw new Exception($message, 90002);
                }
            }
            $this->dbTransaction->commit();
        } catch (Exception $ex) {
            $this->dbTransaction->rollback();
            throw $ex;
        }
    }
    
    private function _setTransactionModel(){
        try {
            $tmodel = new Transactionmodel();
            $this->_setNameSpaces();
            $tmodel->ModelName = $this->classname;
            $tmodel->NameSpace = $this->namespace;
            $pk = $this->getTableSchema()->primaryKey;
            $tmodel->AttributeKey = $pk[0];
            $this->tmodel = $tmodel->getTransactionModel($tmodel);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setNameSpaces(){
        try {
            $this->classname = StringHelper::basename($this->className());
            $namespace = $this->className();
            $_n = explode('\\', $namespace);
            unset($_n[count($_n)-1]);
            $this->namespace = implode("\\", $_n);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setBatch(){
        try {
            $batch = new Transactionbatch();
            $this->batch = $batch->getActiveBatch();
            if(!$this->batch){
                $this->batch = $batch->createBatch();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveLog(){
        try {
            $this->dbTransaction = \Yii::$app->getDb()->beginTransaction();
            $attributes = \Yii::$app->customFunctions->getAttributeDiff($this->_oldAttributes, $this->attributes);
            if(!empty($attributes)){
                $this->_setTransactionModel();
                if($this->tmodel->Enabled == Transactionmodel::STATUS_ACTIVE){
                    $this->syslog = new Syslog();
                    $this->syslog->ActionType = ($this->_newRecord ? Syslog::TYPE_NEWRECORD: Syslog::TYPE_UPDATE);
                    $this->syslog->IdTransactionModel = $this->tmodel->Id;
                    $this->syslog->IdRecord = $this->getPrimaryKey();
                    $this->syslog->Title = $this->syslog->getActionType($this->syslog->ActionType)." de ".$this->tableName()." : ".$this->getPrimaryKey();
                    $this->syslog->ControllerName = $this->_controller;
                    $this->syslog->ActionName = $this->_action;
                    $this->syslog->EnvironmentName = StringHelper::basename(Yii::getAlias('@app'));
                    $this->syslog->IdUser = \Yii::$app->user->getIdentity()->getId();
                    if($this->syslog->save()){
                        
                        $this->syslog->setDetail($attributes);
                    } else {
                        $message = \Yii::$app->customFunctions->getErrors($this->syslog->errors);
                        throw new Exception($message, 90002);
                    }
                }
            }
            $this->dbTransaction->commit();
        } catch (Exception $ex) {
            $this->dbTransaction->rollback();
            throw $ex;
        }
    }
}
