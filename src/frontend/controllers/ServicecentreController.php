<?php

namespace frontend\controllers;

use Yii;
use common\models\Servicecentres;
use backend\models\Appointmentservicesetting;
use backend\models\AppointmentservicesettingSearch;
use backend\models\Settingsdetail;

use frontend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * ServicecentreController implements the CRUD actions for Servicecentres model.
 */
class ServicecentreController extends CustomController 
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionGethours(){
        $response = [];
        $model = new Servicecentres();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = \Yii::$app->request->post('data');
            $data = Json::decode($data, TRUE);
            if(!empty($data['idservicecentre'])){
                $model = $this->findModel($data['idservicecentre']);
                $response = array_merge(['success'=>true],$model->attributes);
            } else {
                $message = 'Debe seleccionar un Duicentreo';
                $model->addError('Id', $message);
                throw new Exception($message, 91001);
                
            }
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }
    
    public function actionGetsuggestion(){
        $response = [];
        $model = new Servicecentres();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = \Yii::$app->request->post('data');
            $data = Json::decode($data, TRUE);
            if(!empty($data['idservicecentre'])){
                $model = $this->findModel($data['idservicecentre']);
                $model->getSuggestions();
                $response = array_merge(['success'=>true],['suggest'=>$model->suggestList]);
            } else {
                $response = ['success'=> TRUE];
            }
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Servicecentres model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Servicecentres the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Servicecentres::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
