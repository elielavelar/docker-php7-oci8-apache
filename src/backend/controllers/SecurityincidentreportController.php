<?php
namespace backend\controllers;

use Yii;
use backend\controllers\CustomController;
use backend\models\Option;
use common\models\Type;
use backend\models\SecurityincidentReports;
use backend\models\Securityincident;
use common\models\Servicecentre;
use backend\models\Settingdetail;

use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use Exception;
/**
 * Description of SecurityincidentreportController
 *
 * @author avelare
 */
class SecurityincidentreportController extends CustomController {
    
    public $customactions = [
        'gettotalevents', 'gettotalbytype','gettotalbycategory', 'gettotalbymonth'
        , 'gettotalbyservicecentre', 'gettotalbyinterrupt'
    ];
    private $_id = null;
    
    public function __construct($id, $module, $config = array()) {
        $this->_id = $id;
        parent::__construct($id, $module, $config);
    }
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
    
    
    public function actionIndex()
    {
        $_action = $this->_getActionName();
        $options = Option::find()
                ->join('INNER JOIN', Option::tableName().' b', Option::tableName().".IdParent = b.Id")
                ->join('INNER JOIN',Type::tableName().' c', Option::tableName().'.IdType = c.Id')
                ->where(['b.KeyWord'=> $this->_id])
                ->andWhere(Option::tableName().".KeyWord != :keyword", [':keyword'=> $_action])
                ->andWhere("c.Code != :code", [':code'=> Option::TYPE_PERMISSION])
                ->all();
        $models = [];
        foreach ($options as $opt){
            if(\Yii::$app->user->can($opt->KeyWord)){
                $models[] = $opt;
            }
        }
        return $this->render('index',['models'=>$models]);
    }
    
    
    public function actionSummary(){
        try {
            $model = new Securityincident();
            $colors = [];
            $types = Type::find()
                    ->where([
                        'KeyWord' => StringHelper::basename(Securityincident::class)
                    ])
                    ->orderBy(['Id' => SORT_ASC])
                    ->all();
            $sectypes = [];
            $model->Year = date('Y');
            foreach ($types as $type){
                $sectypes[] = ['name' => $type->Value, 'title' => $type->Name, 'y' => 0];
            }
            
            /*$colors_set = Settingsdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentres','b.Code'=>'COLORS'])->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
             * 
             */
        } catch (Exception $ex) {
            
        }
        return $this->render('summary',['sectypes' => $sectypes, 'model' => $model, 'colors' => $colors]);
    }
    
    public function actionGettotalevents(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalEvents($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
    
    public function actionGettotalbytype(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalByType($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
    
    public function actionGettotalbycategory(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalByCategory($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
    
    public function actionGettotalbyinterrupt(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalByInterrupt($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
    
    public function actionGettotalbyservicecentre(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalByServiceCentre($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
    
    
    public function actionGettotalbymonth(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecurityincidentReports();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Securityincident::class));
                $model->Year = isset($data['Year']) ? $data['Year']: null;
                $model->IdServiceCentre = isset($data['_idServiceCentre']) ? $data['_idServiceCentre']: null;
                if(isset($data['IncidentDate'])){
                    $model->dateStart = $data['IncidentDate'][0];
                    $model->dateEnd = $data['IncidentDate'][1];
                }
                $result = $model->getTotalByMonth($data);
                $response = [
                    'success' => TRUE,
                    'data'=> $result,
                ];
            } else {
                $message = 'Error en el formato de la solicitud';
                $model->addError('Year',$message);
                throw new Exception($message, 90000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors' => $model->errors
            ];
        }
        return $response;
    }
}
