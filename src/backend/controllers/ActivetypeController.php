<?php

namespace backend\controllers;

use Yii;
use backend\models\Activetype;
use backend\models\ActivetypeSearch;
use backend\models\Problemtype;
use backend\models\ProblemtypeSearch;

use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use Exception;
use yii\web\UploadedFile;

/**
 * ActivetypeController implements the CRUD actions for Activetype model.
 */
class ActivetypeController extends CustomController
{
    
    public $customactions = [
        'get','save', 'getlist',
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Activetype models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActivetypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Activetype model.
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
     * Creates a new Activetype model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Activetype();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Activetype model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $searchModel = new ProblemtypeSearch();
        $searchModel->IdActiveType = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $modelDetail = new Problemtype();
        $modelDetail->IdActiveType = $model->Id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model, 'modelDetail'=> $modelDetail
                , 'dataProvider'=> $dataProvider, 'searchModel'=> $searchModel,
        ]);
    }

    /**
     * Deletes an existing Activetype model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = $this->findModel($id);
            $title = 'Activo';
            $name = $model->Name;
            $dttitle = 'Eliminado';
            if($model->delete()){
                $response = [
                    'success'=>TRUE,
                    'message'=>$title.' '.$name.' '.$dttitle,
                    'title'=>$title.' '.$dttitle,
                ];
            } else {
                $message = $this->setMessageErrors($model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex){
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Activetype model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Activetype the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activetype::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionSave(){
        $model = new Activetype();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post(StringHelper::basename(Activetype::class));
                $dttitle = 'Agregado';
                $id = ArrayHelper::getValue($data, 'Id');
                if(!empty($id)){
                    $model = $this->findModel($id);
                    $dttitle = 'Actualizado';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $model->refresh();
                    $title = 'Activo';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle],$model->attributes);
                } else {
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    throw new Exception($message, 90002);
                }
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
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $option = Activetype::findOne($criteria);
                $response = array_merge(['success'=>TRUE], $option->attributes);
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>FALSE,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }

    public function actionGetlist($q = null, $idparent = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results'=> ['id'=> '','text'=> '']
        ];
        $result['results']= Activetype::getList($q, $idparent);
        return $result;
    }

    public function actionUploadbatch(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Problemtype();
        try {
            $model = new Activetype();
            $model->scenario = Activetype::SCENARIO_UPLOAD;
            $data = Yii::$app->request->post(StringHelper::basename(Activetype::class));
            $model->attributes = $data;
            if (Yii::$app->request->isPost) {
                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
                if(!$model->upload()){
                    $message = $this->getErrors($model->errors);
                    throw new Exception($message, 90000);
                } else {
                    Yii::$app->session->setFlash('success', 'Datos Cargados Exitosamente');
                }
            }
            $response = [
                'success' => true,
                'append' => true,
                'initialPreviewAsData' => true,
                'initialPreviewConfig' => ['key' => $model->IdCategoryType],
                'initialPreview' => false,
            ];
        } catch (Exception $exc) {
            $response = [
                'error' => $exc->getMessage().($model->hasErrors() ? ': '.$this->getErrors($model->errors):'')
            ];
        }
        return $response;
    }
}
