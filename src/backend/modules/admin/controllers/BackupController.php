<?php

namespace backend\modules\admin\controllers;

use yii\web\Controller;
use backend\controllers\CustomController;
use backend\modules\admin\models\BackupSchema;
use yii\helpers\StringHelper;
use Exception;
use yii\web\Response;
use yii\helpers\Json;
use yii\filters\VerbFilter;

/**
 * Default controller for the `backup` module
 */
class BackupController extends CustomController
{
    
    public $customactions = [
        'gethtmllist',
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
    
    public function getModule(){
        return $this->module;
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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        #$this->layout = '\views\layouts\main';
        $model = new BackupSchema();
        return $this->render('index',
            [
                'model'=>$model,
                'dataProvider'=> $model->getList([]),
            ]);
    }
    
    public function actionCreate(){
        $model = new BackupSchema();
        if(\Yii::$app->request->post(StringHelper::basename($model->className()))){
            $post = \Yii::$app->request->post(StringHelper::basename($model->className()));
            $model->attributes = $post;
            $model->backup();
            $url = \Yii::$app->urlManager->createUrl($model->getUrl().$model->file);
            \Yii::$app->session->setFlash('success','Backup Creado Exitosamente <br> '.  \yii\bootstrap\Html::a('<i class="glyphicon glyphicon-download-alt"></i> Descargar',$url));
            return $this->redirect('index');
        } else {
            return $this->render('create', ['model'=>$model]);
        }
    }
    
    public function actionRestore($id){
        $model = new BackupSchema();
        try {
            $model->name = $id;
            $model->restore();
        } catch (Exception $ex) {
            $message = \Yii::$app->customFunctions->getErrors($model->errors);
            $message .= "<br>".$ex->getMessage();
            \Yii::$app->session->setFlash('error', $message);
        }
        return $this->redirect(["index"]);
    }
    
    public function actionDownload($id){
        try {
            $model = new BackupSchema();
            $url = $model->getPath().$id;
            return \Yii::$app->response->sendFile($url);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
   /* 
    public function actionDelete($id){
        try {
            $model = new BackupSchema();
            $model->name = $id;
            $model->delete();
            \Yii::$app->session->setFlash('warning',"Archivo Eliminado Exitosamente");
            return $this->redirect(['index']);
        } catch (Exception $ex) {
            $this->render('site/error', ['name'=>"ERROR ".$ex->getCode(),'message'=>$ex->getMessage(), 'exception'=>$ex]);
        }
    }
    */
    public function actionDelete($id)
    {   
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = new BackupSchema();
            $model->name = $id;
            $model->delete();
            #\Yii::$app->session->setFlash('warning',"Archivo Eliminado Exitosamente");
            $response = [
                'success'=>TRUE,
                'message'=>'Registro Eliminado Exitosamente',
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
            ];
        }
        return $response;
        
//        return $this->redirect(\yii\helpers\Url::to(['setting/update','id'=>$model->IdSetting]));
    }
    
    public function actionGethtmllist(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = new BackupSchema();
                $list = $model->getHtmlBackupList($criteria);
                $response = ['success'=>TRUE, 'list'=>$list];
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionView(){
        $response = ['success'=>TRUE];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = \Yii::$app->request->post('data');
            $values = Json::decode($data, TRUE);
            $model = new BackupSchema();
            $model->attributes = $values;
            $data = $model->getFile();
            $response = array_merge($response, $data);
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }
}
