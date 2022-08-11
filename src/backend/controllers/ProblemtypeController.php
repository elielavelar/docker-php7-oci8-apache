<?php

namespace backend\controllers;

use Yii;
use backend\models\Problemtype;
use backend\models\ProblemtypeSearch;
use backend\models\Problemtypesolution;
use backend\models\search\ProblemtypesolutionSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
use yii\web\UploadedFile;

/**
 * ProblemtypeController implements the CRUD actions for Problemtype model.
 */
class ProblemtypeController extends CustomController
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Updates an existing Problem type model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){
        $model = $this->findModel($id);
        $modelDetail = new Problemtypesolution();
        $modelDetail->IdProblemType = $model->Id;

        $searchModel = new ProblemtypesolutionSearch();
        $searchModel->IdProblemType = $model->Id;
        $dataProvider = $searchModel->search( Yii::$app->getRequest()->getQueryParams());

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
     * Displays a single Problem type model.
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
     * Finds the Problemtype model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Problemtype the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Problemtype::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $option = Problemtype::findOne($criteria);
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
    
    public function actionSave(){
        $model = new Problemtype();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post(StringHelper::basename(Problemtype::class));
                $dttitle = 'Agregado';
                $id = ArrayHelper::getValue($data, 'Id');
                if($id){
                    $model = $this->findModel($id);
                    $dttitle = 'Actualizado';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $model->refresh();
                    $title = 'Tipo de Problema';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle],$model->attributes);
                } else {
                    $message = $this->getErrors($model->errors);
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
    
    public function actionDelete($id)
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = $this->findModel($id);
            $title = 'Tipo de Problema';
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
    
    public function actionGetfilterproblemtypes($q = NULL, $idactivetype = NULL){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results'=> ['id'=> '','text'=> '']
        ];
        $idactivetype = empty($idactivetype) ? NULL: $idactivetype;
        if(!empty($idactivetype)){
            $problemtypes = Problemtype::find()
                    ->select(["id","Name as text"])
                    #->where(['like',"Name", $q])
                    ->where("(:term IS NULL OR Name LIKE '%:term%')",[':term'=> $q])
                    ->andWhere("(:active IS NULL OR :active = IdActiveType )", [':active'=> $idactivetype])
                    ->asArray()
                    ->all();
            $result['results']= $problemtypes;
        }
        return $result;
    }

    public function actionGetlist($q = null, $idparent = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results'=> ['id'=> '','text'=> '']
        ];
        $result['results']= Problemtype::getList($q, $idparent);
        return $result;
    }

    public function actionUploadbatch(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Problemtype();
        try {
            throw new Exception('Error');
            $model->scenario = Problemtype::SCENARIO_UPLOAD;
            $data = Yii::$app->request->post(StringHelper::basename(Problemtype::class));
            $model->attributes = $data;
            //if (Yii::$app->request->isPost) {
            //    $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
            //    if(!$model->upload()){
            //        $message = $this->getErrors($model->errors);
            //        throw new Exception($message, 90000);
            //    } else {
            //        Yii::$app->session->setFlash('success', 'Datos Cargados Exitosamente');
            //    }
            //}
            $response = [
                'success' => true,
            ];
        } catch (Exception $exc) {
            Yii::$app->getResponse()->setStatusCode( 500);
            $response = [
                'error' => $exc->getMessage().($model->hasErrors() ? ': '.$this->getErrors($model->errors, false):'')
            ];
        }
        return $response;
    }
}
