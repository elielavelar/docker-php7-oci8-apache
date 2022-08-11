<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models\traits;
use Yii;
use Exception;

/**
 *
 * @author avelare
 * @param $this common\models\Servicestask;
 */
trait Servicetasktrait {
    //put your code here
    function execNmap(){
        try {
            $response = [];
            $file = null;
            $_path = self::_FILE_PATH_.'/nmap/';
            $path =  \Yii::getAlias($_path);
            if(!file_exists($path)){
                mkdir($path,0777);
            }
            
            $date = date('Ymd_His');
            $filename = "output_".$date."_".$this->Host;
            $filepath = $path.$filename.".xml";
            $script = "nmap -sn -n $this->Host -oX $filepath";
            $result = shell_exec($script);
            if(file_exists($filepath)){
                $file = simplexml_load_file($filepath) or die("Error: Cannot create object");
                $addr = null;
                $values = [];
                foreach($file->host as $key => $val){
                    $this->_iterateObject($val, $values);
                }
                if(!isset($file->host)){
                    $values = [
                        'state' => 'error',
                        'addr' => $this->Host
                    ];
                }
                unlink($filepath);
                $response[$this->Name] = $values;
            } else {
                echo "Error"; die();
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateObject($object, &$values = []){
        try {
            foreach ($object as $key => $val){
                switch (gettype($val)){
                    case 'object':
                        $attr = (array) $val;
                        (isset($attr['@attributes'])) ? $this->_iterateObject($attr, $values) : $this->_iterateObject($val, $values);
                        break;
                    case 'array': 
                        $attr = (array) $val;
                        (isset($attr['@attributes'])) ? $this->_iterateObject($attr, $values) : $this->_iterateObject($val, $values);
                        break;
                    default :
                        $values[$key] = $val;
                        break;
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    function execPing() {
        try {
            $res = null;
            $rval = null;
            $values = shell_exec("ping -w 1 ". $this->Host);
            $result = explode(',', $values);
            return $result;

        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function execHTTPRequest() {
        try {
            
            $port = $this->Port ? $this->Port:80;
            $socket = @fsockopen($this->Host, $port, $this->errorNo, $this->errorStr, 1);
            
            if ($this->errorNo == 0) {
                @fclose($socket);
                return [$this->Name => [
                    'state' => 'up',
                    'addr' => $this->Host
                ]];
            } else {
                return [$this->Name => [
                    'state' => 'error',
                    'addr' => $this->Host
                ]];
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    function execWSDLRequest(){
        try {
            $url = strtolower($this->protocolType->Code).'://'.$this->Host.(!empty($this->Port) ? ':'.$this->Port:'').(!empty($this->Route) ? $this->Route:'');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $xmlData = curl_exec($curl);
            curl_close($curl);
            #$xmlData = @file_get_contents($url);
            #$socket = @fsockopen($this->Host, $port, $this->errorNo, $this->errorStr, 1);
            if(simplexml_load_string($xmlData)) {
                return [
                    $this->Name => [
                        'state' => 'up',
                        'addr' => $url,
                    ],
                ];
            } else {
                return [$this->Name => [
                    'state' => 'error',
                    'addr' => $url
                ]];
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
