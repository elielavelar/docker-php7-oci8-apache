<?php

namespace backend\controllers;

use Yii;
use common\models\Servicetask;
use common\models\search\ServicetaskSearch;
use common\models\Servicetaskcustomstate;
use common\models\search\ServicetaskcustomstateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
/**
 * ServicetaskController implements the CRUD actions for Servicetask model.
 */
class ServicetaskController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Displays a single Servicecentreservices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Updates an existing Servicecentreservices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $modelDetail = new Servicetaskcustomstate();
        $modelDetail->IdServiceTask = $model->Id;
        $modelDetail->IdUserCreate = Yii::$app->getUser()->getIdentity()->getId();
        $modelDetail->userCreateName = Yii::$app->getUser()->getIdentity()->DisplayName;
        
        $searchModel = new ServicetaskcustomstateSearch();
        $searchModel->IdServiceTask = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelDetail' => $modelDetail,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSave(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if (Yii::$app->request->isAjax){
                $data = Yii::$app->request->post(StringHelper::basename(Servicetask::class));
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                } else {
                    $model = new Servicetask();
                }
                $model->attributes = $data;
                if($model->save()){
                    $response = [
                        'success' => true,
                        'title' => 'Tarea Guardada',
                        'message' => 'Tarea Guardada Exitosamente',
                    ];
                } else {
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    throw new Exception($message, 92001);
                }
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'errors' => $model->getErrors(),
            ];
        }
        return $response;
    }
    /**
     * Deletes an existing Servicetask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionDelete($id)
    {   
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        try {
            if($model->delete()){
                #Yii::$app->session->setFlash('warning', 'Valor eliminado Exitosamente');
                $response = [
                    'success'=>TRUE,
                    'message'=>'Registro Eliminado Exitosamente',
                ];
            } else {
                $message = $this->setMessageErrors($model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->Id ? $model->errors:[],
            ];
        }
        return $response;
    }

    /**
     * Finds the Servicetask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Servicetask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Servicetask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if(Yii::$app->request->isAjax){
                $input = Yii::$app->request->post('data');
                $data = Json::decode($input, true);
                $model = $this->findModel($data['Id']);
                if(!empty($model)){
                    $response = $model->attributes;
                    $response['success'] = true;
                } else {
                    throw new Exception('Registro no encontrado',92099);
                }
            }
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ];
        }
        return $response;
    }
    
    public function actionGetvalues(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if(Yii::$app->request->isAjax){
                $input = Yii::$app->request->post('data');
                $data = Json::decode($input, true);
                $model = new Servicetask();
                $response = $model->getValues();
            }
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ];
        }
        return $response;
    }
}
