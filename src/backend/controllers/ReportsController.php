<?php

namespace backend\controllers;
use backend\controllers\CustomController;
use backend\models\Options;
use backend\models\Settingsdetail;
use common\models\Appointments;
use common\models\Servicecentres;
use backend\models\AppointmentsReports;

use common\models\Type;

use Yii;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

class ReportsController extends CustomController
{
    private $_id;
    
    public function __construct($id, $module, $config = array()) {
        $this->_id = $id;
        parent::__construct($id, $module, $config);
    }
    
    public $customactions = [
        'getdatabymonth','getdatabycentre', 'getappointmentsbytype', 'getappointmentbymonth'
        ,'exportsignupbymonth','exportappointmentbymonth','exportdatabycentre','exportsummary'
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
    
    public function actionIndex()
    {
        $_action = $this->_getActionName();
        $options = Options::find()
                ->join('INNER JOIN', 'options b', "b.Id = options.IdParent")
                ->join('INNER JOIN','type c','c.Id = options.IdType')
                ->where(['b.KeyWord'=> $this->_id])
                ->andWhere("options.KeyWord != :keyword", [':keyword'=> $_action])
                ->andWhere("c.Code != :code", [':code'=> Options::TYPE_PERMISSION])
                ->all();
        $models = [];
        foreach ($options as $opt){
            if(\Yii::$app->user->can($opt->KeyWord)){
                $models[] = $opt;
            }
        }
        return $this->render('index',['models'=>$models]);
    }

    public function actionSignupcitizen()
    {
        $colors = [];
        try {
            $model = (object) [
                'showBeforeMonth'=> AppointmentsReports::$showBeforeMonth,
                'includeBeforeMonth'=> AppointmentsReports::$includeBeforeMonth,
                'includeCitizenWithoutApp'=> AppointmentsReports::$includeCitizenWithoutApp
                ];
            $option = Settingsdetail::find()->where(['settingsdetail.Code'=>'IMPL'])
                        ->joinWith('setting b',true)
                        ->andWhere(['b.KeyWord'=>'General','b.Code'=>'IMPL'])
                        ->one();
            $response = [];
            $services = Servicecentres::find()
                    ->select(['servicecentres.Name','servicecentres.IdType'])
                    ->joinWith('type b', false)
                    ->where(['b.Code'=> Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.MBCode'=>'ASC'])
                    ->all();

            $months = \Yii::$app->customFunctions->getMonths();
            
            $centres = [];
            foreach ($services as $service) {
                array_push($centres, $service->Name);
            }
            
            $types = Type::find()->where(['KeyWord'=>'Process'])->orderBy(['Id'=> 'ASC'])->asArray()->all();
            
            $colors_set = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentres','b.Code'=>'COLOR'])->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
            
        } catch (Exception $ex) {
            echo $ex->getTraceAsString();
        }
        return $this->render('signupcitizen',[
                    'setting'=>$option
                    ,'centres'=>$centres
                    ,'types'=> $types
                    ,'colors'=> $colors
                    , 'months' => $months
                    , 'model' => $model
                ]);
    }
    
    public function actionServicecentre()
    {
        try {
            $option = Settingsdetail::find()->where(['settingsdetail.Code'=>'IMPL'])
                        ->joinWith('setting b',true)
                        ->andWhere(['b.KeyWord'=>'General','b.Code'=>'IMPL'])
                        ->one();
            $response = [];
            $services = Servicecentres::find()
                    ->select(['servicecentres.Name','servicecentres.IdType'])
                    ->joinWith('type b', false)
                    ->where(['b.Code'=> Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.MBCode'=>'ASC'])
                    ->all();

            $centres = [];
            foreach ($services as $service) {
                array_push($centres, $service->Name);
            }
            
            $types = Type::find()->where(['KeyWord'=>'Process'])->orderBy(['Id'=> 'ASC'])->asArray()->all();
            
            $months = \Yii::$app->customFunctions->getMonths();
            $colors_set = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentres','b.Code'=>'COLOR'])->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
            
        } catch (Exception $ex) {
            echo $ex->getTraceAsString();
        }
        return $this->render('appointmentservicecentre',['setting'=>$option,'centres'=>$centres,'types'=> $types, 'colors'=> $colors, 'months'=> $months]);
    }
    
    public function actionAppointmenttype(){
        try {
            $option = Settingsdetail::find()->where(['settingsdetail.Code'=>'IMPL'])
                        ->joinWith('setting b',true)
                        ->andWhere(['b.KeyWord'=>'General','b.Code'=>'IMPL'])
                        ->one();
            
            $services = Servicecentres::find()
                    ->select(['servicecentres.Name','servicecentres.IdType'])
                    ->joinWith('type b', false)
                    ->where(['b.Code'=> Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.MBCode'=>'ASC'])
                    ->all();
            
            $type = Type::find()
                    ->select(['type.Name','IdState'])
                    ->joinWith('state b')
                    ->where(['type.KeyWord'=> 'Process','b.Code' => Type::STATUS_ACTIVE])
                    ->all();
            $types = \yii\helpers\ArrayHelper::map($type, 'Name', 'Name');
            $types = explode(",", implode(",", $types));
            
            $centres = [];
            foreach ($services as $service) {
                array_push($centres, $service->Name);
            }
            
            $months = \Yii::$app->customFunctions->getMonths();
            $colors_set = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentres','b.Code'=>'COLOR'])->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
            
            return $this->render('appointmenttype',['centres'=> $centres,'setting'=> $option, 'types'=> $types,'months'=>$months,'colors'=>$colors]);
        } catch (Exception $ex) {
            \Yii::$app->errorHandler->handleException($ex);
            return;
        }
    }
    
    
    public function actionSummary(){
        try {
            
            $model = (object) [
                'includeBeforeMonth'=> AppointmentsReports::$includeBeforeMonth,
                'showBeforeMonth'=> AppointmentsReports::$showBeforeMonth,
                'includeCitizenWithoutApp'=> AppointmentsReports::$includeCitizenWithoutApp
                ];
            $option = Settingsdetail::find()->where(['settingsdetail.Code'=>'IMPL'])
                        ->joinWith('setting b',true)
                        ->andWhere(['b.KeyWord'=>'General','b.Code'=>'IMPL'])
                        ->one();
            $response = [];
            $services = Servicecentres::find()
                    ->select(['servicecentres.Name','servicecentres.IdType'])
                    ->joinWith('type b', false)
                    ->where(['b.Code'=> Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.MBCode'=>'ASC'])
                    ->all();

            $centres = [];
            foreach ($services as $service) {
                array_push($centres, $service->Name);
            }
            
            $types = Type::find()->where(['KeyWord'=>'Process'])->orderBy(['Id'=> 'ASC'])->asArray()->all();
            
            $months = \Yii::$app->customFunctions->getMonths();
            $colors_set = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentres','b.Code'=>'COLOR'])->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
        } catch (Exception $ex) {
            \Yii::$app->errorHandler->handleException($ex);
            return;
        }
        return $this->render('summary',[
                'centres'=> $centres,'colors'=> $colors, 'months'=> $months, 'types'=> $types,'setting'=> $option
                , 'model'=> $model
            ]);
    }




    public function actionGetdatabymonth(){
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $input = \Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $response = [];
            
            $records = AppointmentsReports::getSignUpByMonth($data);
            $response = [
                'success'=>TRUE,
                'data'=>$records,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>false,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionGetappointmentbymonth(){
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $input = \Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $response = [];
            
            $records = AppointmentsReports::getAppointmentByMonth($data);
            $response = [
                'success'=>TRUE,
                'data'=>$records,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>false,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }

    public function actionGetdatabycentre(){
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $input = \Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $response = [];
            
            $records = AppointmentsReports::getSignUpByCentre($data);
            $response = [
                'success'=>TRUE,
                'data'=>$records,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionGetappointmentsbytype(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $input = \Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $result = AppointmentsReports::getAppointmentByType($data);
            $response = [
                'success' => TRUE,
                'data'=> $result,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionExportsignupbymonth(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $input = Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $report = AppointmentsReports::exportSignUpByMonth($data);
            $response = [
                'success'=> TRUE,
                'message'=> 'Reporte Generado Exitosamente',
                'url'=> $report,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionExportappointmentbymonth(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $input = Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $report = AppointmentsReports::exportAppointmentByMonth($data);
            $response = [
                'success'=> TRUE,
                'message'=> 'Reporte Generado Exitosamente',
                'url'=> $report,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionExportdatabycentre(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $input = Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $report = AppointmentsReports::exportDataByCentre($data);
            $response = [
                'success'=> TRUE,
                'message'=> 'Reporte Generado Exitosamente',
                'url'=> $report,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionExportsummary(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $input = \Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            
            $report = AppointmentsReports::exportSummary($data);
            $response = [
                'success'=> TRUE,
                'message'=> 'Reporte Generado Exitosamente',
                'url'=> $report,
            ];
            
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        
        return $response;
    }
}
