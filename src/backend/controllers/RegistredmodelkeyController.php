<?php

namespace backend\controllers;

use Yii;
use common\models\Registredmodelkeys;
use backend\models\RegistredmodelkeySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * RegistredmodelkeyController implements the CRUD actions for Registredmodelkeys model.
 */
class RegistredmodelkeyController extends Controller
{
    public $customactions = [];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    /*
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
     * 
     */

    /**
     * Lists all Registredmodelkeys models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Registredmodelkeys();
        $searchModel = new RegistredmodelkeySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Registredmodelkeys model.
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
     * Creates a new Registredmodelkeys model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registredmodelkeys();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Registredmodelkeys model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSave(){
        $model = new Registredmodelkeys();
        
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Registredmodelkeys::class));
                $title = 'Llave';
                $dttitle = 'Agregada';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $dttitle = 'Actualizada';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $name = $model->AttributeKeyName;
                    $model->refresh();
                    $title = 'Detalle';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle, 'message'=>$title.' '.$name.' '.$dttitle],$model->attributes);
                } else {
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    throw new Exception($message, 90002);
                }
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>false,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    public function actionDelete($id)
    {   
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        try {
            $name = $model->AttributeKeyName;
            if($model->delete()){
                #Yii::$app->session->setFlash('warning', 'Valor eliminado Exitosamente');
                $response = [
                    'success'=>true,
                    'title'=>'Eliminación de Registro',
                    'message'=>'Llave '.$name.' Eliminada Exitosamente',
                ];
            } else {
                $message = Yii::$app->customFunctions->getErrors($model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=>false,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->Id ? $model->errors:[],
            ];
        }
        return $response;
    }


    /**
     * Finds the Registredmodelkeys model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registredmodelkeys the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registredmodelkeys::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodelkeys();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Registredmodelkeys::findOne($criteria);
                $response = array_merge(['success'=>true],$model->attributes);
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>false,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
                'errors' => $model->errors,
            ];
        }
        return $response;
    }
 
}
