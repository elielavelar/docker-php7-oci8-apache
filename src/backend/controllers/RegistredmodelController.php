<?php

namespace backend\controllers;

use Yii;
use common\models\Registredmodel;
use common\models\search\RegistredmodelSearch;
use common\models\Registredmodelkey;
use common\models\search\RegistredmodelkeySearch;
use yii\web\Controller;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * RegistredmodelController implements the CRUD actions for Registredmodels model.
 */
class RegistredmodelController extends CustomController
{
    public $customactions = [
        'get','getmodelattributes','getmodelattributesform','getmodelattributeslist','getmodels','getmodelvalues'
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }

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
     * Lists all Registredmodel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Registredmodel();
        $searchModel = new RegistredmodelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Registredmodel model.
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
     * Creates a new Registredmodel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registredmodel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Registredmodel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetail = new Registredmodelkey();
        $modelDetail->IdRegistredModel = $model->Id;
        $searchModel = new RegistredmodelkeySearch();
        $searchModel->IdRegistredModel = $model->Id;
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

    /**
     * Deletes an existing Registredmodel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Registredmodels model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registredmodels the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registredmodel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Registredmodel::findOne($criteria);
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
    
    public function actionGetmodels(){
        $response = [
            'results'=> ['id'=> '','text'=> '']
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->get();
                $model->attributes = $data;
                $model->term = isset($data['q']) ? $data['q'] : '';
                $response = $model->getModels();
            }
        } catch (Exception $exc) {
            print_r($exc->getMessage());
        }
        return $response;
    }
    
    public function actionGetmodelattributes(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post();
                $model = Registredmodel::findOne($data);
                if($model){
                    $response = $model->getModelAttributes();
                }
                $response['success'] = true;
            }
        } catch (Exception $exc) {
            $response['success'] = false;
            $response['message'] = $exc->getMessage();
            $response['code'] = $exc->getCode();
            $response['errors'] = $model->getErrors();
        }
        return $response;
    }
    
    public function actionGetmodelattributeslist(){
        $response = [
            'results'=> ['id'=> '','text'=> '']
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->get();
                $model = Registredmodel::findOne($data);
                $response = $model->getModelAttributesList();
                $response['success'] = true;
            }
        } catch (Exception $exc) {
            $response['success'] = false;
            $response['message'] = $exc->getMessage();
            $response['code'] = $exc->getCode();
            $response['errors'] = $model->getErrors();
        }
        return $response;
    }
    
    public function actionGetmodelattributesform(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, true);
                $model = Registredmodel::findOne($criteria);
                $response = ['success'=>true, 'input' => $model->getModelAttributesForm()];
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
    
    public function actionGetmodelvalues(){
        $response = [
            'results'=> ['id'=> '','text'=> '']
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Registredmodel();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post();
                $model = Registredmodel::findOne(['Id' => $data['Id']]);
                $model->keyAttribute = isset($data['keyAttribute'])? $data['keyAttribute']: null;
                unset($data['keyAttribute']);
                $attributes = $model->getAttributes();
                $criteria = [];
                foreach ($data as $key => $value ){
                    
                    if(!array_key_exists($key, $attributes)){
                        $criteria[$key] = $value; 
                    } 
                }
                $model->criteria = $criteria;
                $response = $model->getModelAttributesValueList();
                $response['success'] = true;
            }
        } catch (Exception $exc) {
            $response['success'] = false;
            $response['message'] = $exc->getMessage();
            $response['code'] = $exc->getCode();
            $response['errors'] = $model->getErrors();
        }
        return $response;
    }
}
