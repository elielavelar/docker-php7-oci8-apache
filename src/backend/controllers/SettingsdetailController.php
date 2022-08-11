<?php

namespace backend\controllers;
use \backend\models\Settingsdetail;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

class SettingsdetailController extends \yii\web\Controller
{
    public function actionCreate()
    {
        $model = new Settings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        return $this->render('create');
    }

    public function actionSave(){
        $model = new Settingsdetail();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('Settingsdetail');
//                $data = json_decode($data, TRUE);
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;

                if($model->save()){
                    $response = array_merge(['success'=>true],$model->attributes);
                } else {
                    $message = $this->setMessageErrors($model->errors);
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
        echo json_encode($response);
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
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = json_decode($data, TRUE);
                $model = $this->findModel($data['id']);
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
        echo json_encode($response);
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
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settingsdetail::findOne($id)) !== null) {
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
