<?php

namespace backend\controllers;

use common\models\Attachment;
use Yii;
use backend\models\Incidentcategory;
use backend\models\IncidentcategorySearch;
use backend\models\Activetype;
use backend\models\ActivetypeSearch;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\CustomController;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
use yii\web\UploadedFile;

/**
 * IncidentcategoryController implements the CRUD actions for Incidentcategory model.
 */
class IncidentcategoryController extends CustomController
{
    
    public $customactions = [
        'get','save','gethtmllist', 'getlist', 'getlistbyresourcetype'
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
     * Lists all Incidentcategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Incidentcategory();
        $model->IdParent = NULL;
        $searchModel = (new Incidentcategory())->getGridList();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchModel,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=> $model,
        ]);
    }

    /**
     * Displays a single Incidentcategory model.
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
     * Creates a new Incidentcaterogy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->getQueryParams();
        $model = new Incidentcategory();
        $IdParent = (int) ArrayHelper::getValue($data, 'id');
        $model->IdParent = $IdParent ?: null;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Incidentcategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $searchModelActive = new ActivetypeSearch();
        $searchModelActive->IdCategoryType = $model->Id;
        $dataProviderActive = $searchModelActive->search(Yii::$app->request->queryParams);

        $modelDetail = new Incidentcategory();
        $modelDetail->IdParent = $model->Id;
        $searchModel = new IncidentcategorySearch();
        $searchModel->IdParent = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $modelActive = new Activetype();
        $modelActive->IdCategoryType = $model->Id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel'=> $searchModel,
            'modelDetail' => $modelDetail,
            'dataProvider'=> $dataProvider,
            'searchModelActive'=> $searchModelActive,
            'modelActive' => $modelActive,
            'dataProviderActive'=> $dataProviderActive,
        ]);
    }

    /**
     * Deletes an existing Incidentcategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /**
     * Deletes an existing Options model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = $this->findModel($id);
            $title = 'Categoría';
            $name = $model->Name;
            $dttitle = 'Eliminada';
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
     * Finds the Incidentcaterogy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incidentcaterogy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incidentcategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionSave(){
        $model = new Incidentcategory();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('Incidentcategory');
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
                    $model->refresh();
                    $title = 'Categoría';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle],$model->attributes);
                } else {
                    #$message = $this->setMessageErrors($model->errors);
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
        #echo json_encode($response);
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $option = Incidentcategory::findOne($criteria);
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
    
    public function actionGethtmllist(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = new Incidentcategory();
            $model->IdParent = NULL;
            $model->getHTMLList();
            $response = [
                'success'=>TRUE,
                'list'=>$model->htmlList,
            ];
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
        $result['results']= Incidentcategory::getList($q, $idparent);
        return $result;
    }

    public function actionUploadbatch(){
        try {
            $model = new Incidentcategory();
            $model->scenario = Incidentcategory::SCENARIO_UPLOAD;
            $data = Yii::$app->request->post(StringHelper::basename(Incidentcategory::class));
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
            return $this->redirect(Yii::$app->request->referrer ?: 'index');
        } catch (Exception $exc) {
            echo false;
            print_r($model->errors);
            echo $exc->getMessage();
        }
    }

}
