<?php

namespace backend\controllers;

use Yii;
use common\models\Extendedmodelkey;
use common\models\Extendedmodelfield;
use common\models\Extendedmodelfieldgroup;
use common\models\search\ExtendedmodelfieldgroupSearch;
use common\models\search\ExtendedmodelfieldSearch;
use backend\controllers\BasicController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * ExtendedmodelkeyController implements the CRUD actions for Extendedmodelkey model.
 */
class ExtendedmodelkeyController extends BasicController
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
    public function actionCreate($id){
        $model = new Extendedmodelkey();
        $model->IdExtendedModel = $id;
        if($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post(StringHelper::basename(Extendedmodelkey::class));
            $conditions = isset($data['condition']) ? $data['condition'] : [];
            $model->setCondition($conditions);
            $model->value = isset($data['value']) ? $data['value'] : null;
            if($model->save()){
                return $this->redirect(['update', 'id' => $model->Id]);
            } 
        }
        if($model->EnabledModelSource){
            $model->conditionform = $model->loadModelAttributesForm();
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }
    
    /**
     * Updates an existing Extendedmodels model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->EnabledModelSource ? $model->getModelAttributes() : null;
        
        $modelField = new Extendedmodelfield();
        $modelField->Sort = $modelField::SORT_DEFAULT_VALUE;
        $modelField->Required = $modelField::REQUIRED_DISABLED;
        $modelField->ColSpan = $modelField::COLS_DEFAULT_VALUE;
        $modelField->RowSpan = $modelField::ROWS_DEFAULT_VALUE;
        $modelField->idExtendedModel = $model->IdExtendedModel;
        
        $modelDetail = new Extendedmodelfieldgroup();
        $modelDetail->IdExtendedModelKey = $model->Id;
        $modelDetail->Sort = $modelDetail::SORT_DEFAULT_VALUE;
        $modelDetail->VisibleContainer = $modelDetail::VISIBLE_CONTAINER_DISABLED;
        
        $searchModel = new ExtendedmodelfieldgroupSearch();
        $searchModel->IdExtendedModelKey = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $modelGroup = new Extendedmodelfieldgroup();
        $modelGroup->IdExtendedModelKey = $model->Id;

        if($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post(StringHelper::basename(Extendedmodelkey::class));
            $conditions = isset($data['condition']) ? $data['condition'] : [];
            $model->setCondition($conditions);
            $model->value = isset($data['value']) ? $data['value'] : null;
            if($model->save()){
                return $this->redirect(['update', 'id' => $model->Id]);
            } 
        }
        if($model->EnabledModelSource){
            $model->conditionform = $model->loadModelAttributesForm();
        }
        return $this->render('update', [
            'model' => $model
                , 'modelDetail' => $modelDetail
                , 'searchModel' => $searchModel
                , 'dataProvider' => $dataProvider
                , 'modelField' => $modelField
        ]);
    }

    /**
     * Finds the Extendedmodelkey model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Extendedmodelkey the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Extendedmodelkey::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Extendedmodelkey();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Extendedmodelkey::findOne($criteria);
                $response = array_merge(['success'=>true],$model->attributes);
                $response['conditions'] = $model->loadModelAttributesForm();
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
    
    public function actionSave(){
        $model = new Extendedmodelkey();
        
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Extendedmodelkey::class));
                $title = 'Llave';
                $dttitle = 'Agregada';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $dttitle = 'Actualizada';
                    if($model == null){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $conditions = isset($data['condition']) ? $data['condition'] : [];
                $model->attributes = $data;
                $model->setCondition($conditions);
                $model->value = isset($data['value']) ? $data['value'] : null;
                if($model->save()){
                    $name = $model->AttributeKeyName;
                    $model->refresh();
                    $title = 'Detalle';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle, 'message'=>$title.' '.$name.' '.$dttitle],$model->attributes);
                } else {
                    $message = $this->getErrors($model->errors);
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
                $message = $this->setMessageErrors($model->errors);
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
    
    public function actionGetmodelattributesform(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Extendedmodelkey();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, true);
                $model = new Extendedmodelkey();
                $model->idRegistredModel = $criteria['Id'];
                $model->lastId = isset($criteria['lastId']) ? $criteria['lastId'] : null;
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
    
}
