<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace common\models;

use Yii;
use common\models\CustomBaseActiveRecord;
use common\models\Transaction;
use common\models\Transactionbatch;
use common\models\Transactionmodel;
use common\models\Registredmodel;
use common\models\Extendedmodel;
use common\models\Syslog;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use Exception;
use backend\models\Option;

/**
 * Description of CustomActiveRecord
 *
 * @author avelare
 */
class CustomActiveRecord extends CustomBaseActiveRecord {
    private $batch = null;
    private $transaction;
    private $syslog;
    protected $searchModel = false;
    private $dbTransaction;
    private $model;
    private $tmodel;
    public $namespace = null;
    public $completepath = null;
    public $classname = null;
    private $_attributes = [];
    private $_oldAttributes = [];
    private $_newRecord = true;
    private $_pk = null;
    private $_isExteded = false;
    private $_user = null;
    
    private $saveTransaction = false;
    private $saveLog = false;
    private $_controller = null;
    private $_action = null;
    private $_actionKey = null;
    private $enable = false;
    public $dynamicform = null;
    public $dynamicformhtml = '';
    public $dynamicformjsvalidation = '';
    public $dynamicfields = null;
    public $Extendedmodelfield = null;
    private $_extendedmodelrecord = null;
    private $_hasDynamicFields = false;
    private $_dynamickeys = null;
    private $_defaultdynamickey = null;
    private $_defaultdynamicfields = [];
    private $_dynamicfields = [];
    private $_dynamicfieldJsValidation = [];
    private $_dynamicfieldvalues = [];
    private $form = null;
    private $_loadedExtended = false;
    
    public function __construct($config = array()) {
        try {
            $this->_controller = isset(\Yii::$app->controller) ? \Yii::$app->controller->id : null;
            $this->enable = $this->_controller ? true: false;
            $this->_action = isset(\Yii::$app->controller->action->id) ? \Yii::$app->controller->action->id : null;
            $this->_enabledExtendedOptions();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::__construct($config);
    }
    
    public function getAction(): string {
        return $this->_action;
    }
    
    public function hasDynamicFields(): bool {
        return $this->_hasDynamicFields;
    }

    public function setSaveTransaction($setting = false) {
        $this->saveTransaction = $setting;
    }

    public function setSaveLog($setting = false) {
        $this->saveLog = $setting;
    }
    
    public function getForm() : \kartik\widgets\ActiveForm {
        return $this->form;
    }
    
    public function setForm(\kartik\widgets\ActiveForm $form = null){
        $this->form = $form;
    }
    
    private function _getUser(){
        try {
            $this->_user = ( in_array(StringHelper::basename(Yii::getAlias('@app')), ['client','console']) ) ? $this->_user = null : \Yii::$app->user->getIdentity();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    private function _setNameSpaces(){
        try {
            $this->classname = StringHelper::basename($this->className());
            $namespace = $this->className();
            $_n = explode('\\', $namespace);
            $k = array_splice($_n, 0, count($_n)-1);
            $this->namespace = reset($k);
            $this->completepath = implode("\\", $k);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setModel(){
        try {
            if($this->enable){
                $this->_setNameSpaces();
                $this->model = Registredmodel::findOne(['KeyWord' => $this->classname,'NameSpace' => $this->namespace]);
                if(empty($this->model)){
                    $this->_saveModel();
                } 
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _saveModel(){
        try {
            $this->model = new Registredmodel();
            $this->model->Name = $this->classname;
            $this->model->KeyWord = $this->classname;
            $this->model->NameSpace = $this->namespace; //StringHelper::basename(Yii::getAlias('@app'));
            $this->model->CompletePath = $this->completepath;
            $this->model->EnableExtended = Registredmodel::EXTENDEDMODEL_DISABLED;
            $this->model->keys = $this->getPrimaryKey(true);
            if($this->model->save()){
                $this->model->refresh();
            } else {
                $message = \Yii::$app->customFunctions->getErrors($this->model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setExtended(){
        try {
            if($this->enable){
                (!$this->model) ? $this->_setModel() : null;
                $this->_isExteded = $this->model ? $this->model->EnableExtended == Registredmodel::EXTENDEDMODEL_ENABLED : false ;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function isExtended(){
        return $this->_isExteded;
    }
    
    private function _getSettingOption(){
        try {
            if($this->enable){
                $option = Option::findOne(['KeyWord'=> $this->_actionKey]);
                if($option){
                    $this->saveTransaction = $option->SaveTransaction == 1;
                    $this->saveLog = $option->SaveLog == Option::SAVE_LOG_ENABLED;
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _enabledExtendedOptions(){
        try {
            if($this->enable && !$this->searchModel && !$this->_loadedExtended){
                ($this->_controller && $this->_action) ?  $this->_actionKey = $this->_controller. ucfirst($this->_action) : null;
                $this->_getUser();
                $this->enable = $this->enable && !empty($this->_user);
                $this->_getSettingOption();
                $this->_setModel();
                $this->_setExtended();
                $this->_isExteded ? $this->_getDefaultDynamicFields() : null;
                $this->_loadedExtended = true;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getDefaultDynamicFields(){}
}
