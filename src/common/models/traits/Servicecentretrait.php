<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models\traits;
use Yii;
use Exception;
use yii\helpers\StringHelper;
use backend\models\Settingdetail;
use backend\models\Setting;
use common\models\State;
use common\models\Servicecentre;
/**
 *
 * @author avelare
 * @var $this Servicecentre;
 */
trait Servicecentretrait {
    /**
     * @var $this Servicecentre;
     */
    private $values = [];
    private $hosts = [];
    private $hostservicecentre = [];
    private $response = [];
    private $_currentCentre = null;
    private $_currentService = null;
    private $_currentTask = null;
    private $filesourcepath = null;
    private $filesource = null;
    
    public function getDataServicesStatus(){
        try {
            $response = [];
            /*$centres = self::find()
                    ->joinWith('state b')
                    ->joinWith('type c')
                    ->where([
                        'b.Code' => self::STATE_ACTIVE,
                    ])
                    ->andWhere('(c.Code IN(:duisite, :datacenter))',[':duisite' => self::TYPE_DUISITE,':datacenter' => self::TYPE_DATACENTER])
                    ->all();
             * 
             */
            $centres = self::find()
                ->joinWith('state b')
                ->joinWith('type c')
                ->innerJoin(Settingdetail::tableName().' d','d.Code = c.Code')
                ->innerJoin(Setting::tableName().' e','e.Id = d.IdSetting')
                ->innerJoin(State::tableName().' f','f.Id = d.IdState')
                ->where([
                    'b.Code' => self::STATE_ACTIVE,
                    'e.KeyWord' => StringHelper::basename(self::class),
                    'e.Code' => 'MON',
                    'f.Code' => \backend\models\Setting::STATUS_ACTIVE,
                    Servicecentre::tableName().'.EnabledMonitoring' => self::MONITORING_ENABLED,
                ])
                #->andWhere('(c.Code IN(:duisite, :datacenter))',[':duisite' => Servicecentres::TYPE_DUISITE,':datacenter' => Servicecentres::TYPE_DATACENTER])
                ->all();        
            foreach ($centres as $centre){
                $this->_currentCentre = $centre;
                $this->response[$this->_currentCentre->Id] = [];
                $this->_iterateServices();
            }
            (!empty($this->hosts)) ? $this->execNmap() : null;
            return !empty($this->response) ? ['success' => true, 'values' => $this->response]: ['success'=> false, 'code' => 96000 ,'message' => 'Error al recuperar datos'];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateServices(){
        try {
            foreach ($this->_currentCentre->services as $serv){
                if($serv->state->Code == 'ACT'){
                    $this->_currentService = $serv;
                    $this->response[$this->_currentCentre->Id][$this->_currentService->Code] = [];
                    $this->_iterateTasks();
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateTasks(){
        try {
            foreach ($this->_currentService->servicetasks as $task){
                $this->_currentTask = $task;
                $this->response[$this->_currentCentre->Id][$this->_currentService->Code] = $this->_getStatus();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getStatus(){
        try {
            $response = [];
            switch ($this->_currentTask->type->Code){
                case 'PING':
                    array_push($this->hosts, $this->_currentTask->Host);
                    $this->hostservicecentre[$this->_currentTask->Host]['Centre'] = $this->_currentTask->service->serviceCentre->Id;
                    $this->hostservicecentre[$this->_currentTask->Host]['Service'] = $this->_currentTask->service->Code;
                    $this->hostservicecentre[$this->_currentTask->Host]['Task'] = $this->_currentTask->Name;
                    break;
                case 'WSP':
                    $response = $this->_currentTask->execWSDLRequest();
                    break;
                case 'HTTP':
                default :
                    $response = $this->_currentTask->execHTTPRequest();
                    break;
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
            
    private function _createFile(){
        try {
            $file = null;
            $_path = self::_FILE_PATH_.'/';
            $path =  \Yii::getAlias($_path);
            if(!file_exists($path)){
                mkdir($path,0777, true);
            }
            $date = date('Ymd_His');
            $filename = "host_".$date;
            $this->filesourcepath = $path.$filename.".txt";
            $this->filesource= fopen($this->filesourcepath, 'w') or die('Error to create File '.$this->filesourcepath."!");
            
            $txt = '';
            foreach ($this->hosts as $host){
                $txt .= "$host\n";
            }
            fwrite($this->filesource, $txt);
            fclose($this->filesource);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    //put your code here
    private function execNmap(){
        try {
            $this->_createFile();
            $response = [];
            $file = null;
            $_path = self::_FILE_PATH_.'/';
            $path =  \Yii::getAlias($_path);
            if(!file_exists($path)){
                mkdir($path,0777);
            }
            
            $date = date('Ymd_His');
            $filename = "output_hosts_".$date;
            $filepath = $path.$filename.".xml";
            $script = "nmap -sn -n -iL $this->filesourcepath -oX $filepath";
            $result = shell_exec($script);
            if(file_exists($filepath)){
                $file = simplexml_load_file($filepath) or die("Error: Cannot create object");
                $addr = null;
                
                foreach($file->host as $key => $val){
                    $values = [];
                    $this->_iterateObject($val, $values);
                    $response[] = $values;
                }
                
                unlink($filepath);
                
            } else {
                echo "Error"; die();
            }
            unlink($this->filesourcepath);
            $this->_formatDataNmap($response);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _formatDataNmap($results){
        try {
            foreach ($results as $result){
                if(isset($this->hostservicecentre[$result['addr']])){
                    $this->hostservicecentre[$result['addr']]['succes'] = true;
                    $k = array_search($result['addr'], $this->hosts, true);
                    unset($this->hosts[$k]);
                    $centre = $this->hostservicecentre[$result['addr']]['Centre'];
                    $service = $this->hostservicecentre[$result['addr']]['Service'];
                    $task = $this->hostservicecentre[$result['addr']]['Task'];
                    $r = [
                        $service => [
                            $task => $result,
                        ]
                    ];
                    $this->response[$centre] = array_merge($this->response[$centre], $r);
                }
            }
            foreach ($this->hosts as $host){
                if(isset($this->hostservicecentre[$host])){
                    $centre = $this->hostservicecentre[$host]['Centre'];
                    $service = $this->hostservicecentre[$host]['Service'];
                    $task = $this->hostservicecentre[$host]['Task'];
                    $r = [
                        $service => [
                            $task => [
                                'state' => 'error',
                                'addr' => $host,
                            ],
                        ]
                    ];
                    $this->response[$centre] = array_merge($this->response[$centre], $r);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateObject($object, &$values = []){
        try {
            foreach ($object as $key => $node){
                switch (gettype($node)){
                    case 'object':
                        $attr = (array) $node;
                        (isset($attr['@attributes'])) ? $this->_iterateObject($attr, $values) : $this->_iterateObject($node, $values);
                        break;
                    case 'array': 
                        $attr = (array) $node;
                        (isset($attr['@attributes'])) ? $this->_iterateObject($attr, $values) : $this->_iterateObject($node, $values);
                        break;
                    default :
                        $values[$key] = $node;
                        break;
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
