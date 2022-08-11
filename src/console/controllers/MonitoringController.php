<?php

namespace console\controllers;

use console\models\Servicecentres;
use yii\console\Controller;
use Exception;
use Yii;

class MonitoringController extends Controller
{
    
    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        //$authKey = \Yii::$app->params["authKey"];
        //$this->logon($authKey);
        
    }
    
    public function actionGetdata(){
        try {
            $model = new Servicecentres();
            $response = $model->getDataServicesStatus();
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ];
        }
        print_r($response);
    }
}