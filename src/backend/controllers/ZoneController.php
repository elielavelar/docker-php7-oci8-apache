<?php

namespace backend\controllers;

use Yii;
use common\models\Zone;
use backend\models\ZoneSearch;
use common\models\Zonesupervisors;
use backend\models\ZonesupervisorSearch;
use common\models\Servicecentre;
use backend\models\ServicecentresSearch;
use yii\web\Controller;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
/**
 * ZoneController implements the CRUD actions for Zones model.
 */
class ZoneController extends CustomController
{
    
    public $customactions = [
        'get','save'
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
    
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

    /**
     * Lists all Zones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ZoneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Zone();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }
    
    public function actionCreate(){
        $model = new Zone();
        return $this->render('create', ['model' => $model]);
    }
    
    public function actionUpdate($id){
        $model = $this->findModel($id);
        
        $modelService = new Servicecentre();
        $modelService->IdZone = $model->Id;
        
        $searchService = new ServicecentresSearch();
        $searchService->IdZone = $model->Id;
        $dataProviderService = $searchService->search(\Yii::$app->request->queryParams);
        
        $modelDetail = new Zonesupervisors();
        $modelDetail->IdZone = $model->Id;
        
        $searchModel = new ZonesupervisorSearch();
        $searchModel->IdZone = $model->Id;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('update', [
            'model' => $model, 'modelDetail' => $modelDetail, 'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'searchService' => $searchService, 'dataProviderService' => $dataProviderService,
            'modelService' => $modelService,
        ]);
    }

    public function actionSave(){
        $model = new Zone();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('Zone');
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $response = array_merge(['success'=>true],$model->attributes);
                } else {
                    $message = $this->setMessageErrors($model->errors);
                    throw new Exception($message, 90002);
                }
                $response = array_merge(['success'=>true],$model->attributes);
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = Json::decode($data, TRUE);
                $model = $this->findModel($data['id']);
                if($model == NULL){
                    throw new Exception('No se encontró registro', 90001);
                }
                $response = array_merge(['success'=>true],$model->attributes);
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
            ];
        }
        return $response;
    }

    /**
     * Deletes an existing Zones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Zones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Zones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Zone::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
