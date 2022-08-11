<?php

namespace backend\controllers;
use \backend\models\Settingdetail;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;

class SettingdetailController extends \yii\web\Controller
{
    public function actionSave(){
        $model = new Settingdetail();
        
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Settingdetail::class));
                $title = 'Registro';
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
                    $name = $model->Name;
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

    public function actionGet(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {

            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = json_decode($data, TRUE);
                $model = $this->findModel($data['Id']);
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

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionView()
    {
        return $this->render('view');
    }
    
    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Setting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settingdetail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    private function setMessageErrors($errors){
        $message = '';
        if(!empty($errors)){
            foreach ($errors as $error){
                $message  .= (implode("- ", $error)).'<br/>';
            }
        }
        return $message;
    }

}