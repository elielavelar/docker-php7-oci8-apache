<?php

namespace backend\controllers;

use Yii;
use common\models\Fieldcatalog;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * FieldcatalogController implements the CRUD actions for Fieldscatalogs model.
 */
class FieldcatalogController extends CustomController
{
    public $customactions = [
        'get','save',
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
   
    public function actionSave(){
        $model = new Fieldcatalog();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Fieldcatalog::class));
                $dttitle = 'Agregado';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $dttitle = 'Actualizado';
                    if($model == null){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $name = $model->Name;
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
            $name = $model->Name;
            if($model->delete()){
                #Yii::$app->session->setFlash('warning', 'Valor eliminado Exitosamente');
                $response = [
                    'success'=>true,
                    'title'=>'Eliminación de Registro',
                    'message'=>'Valor '.$name.' Eliminado Exitosamente',
                ];
            } else {
                $message = $this->getErrors($model->errors);
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
     * Finds the Fieldscatalogs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fieldscatalogs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fieldcatalog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Fieldcatalog();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Fieldcatalog::findOne($criteria);
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
