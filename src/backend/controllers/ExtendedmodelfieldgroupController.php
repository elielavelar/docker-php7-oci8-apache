<?php

namespace backend\controllers;

use Yii;
use common\models\Extendedmodelfieldgroup;
use backend\controllers\BasicController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * Description of ExtendedmodelfieldgroupController
 *
 * @author eliel
 */
class ExtendedmodelfieldgroupController extends BasicController {
    //put your code here
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Extendedmodelfieldgroup();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Extendedmodelfieldgroup::findOne($criteria);
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
    
    public function actionSave(){
        $model = new Extendedmodelfieldgroup();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Extendedmodelfieldgroup::class));
                $dttitle = 'Agregado';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $dttitle = 'Actualizado';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if($model->save()){
                    $model->refresh();
                    $title = 'Grupo';
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
    
    public function actionGetmodelfieldgroups(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = Yii::$app->request->post('data');
            $criteria = Json::decode($data);
            $model = new Extendedmodelfieldgroup();
            $model->IdExtendedModelKey = $criteria['IdExtendedModelKey'];
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
    
    
    /**
     * Finds the Fieldscatalogs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fieldscatalogs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Extendedmodelfieldgroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
                    'message'=>'Grupo '.$name.' Eliminado Exitosamente',
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

}
