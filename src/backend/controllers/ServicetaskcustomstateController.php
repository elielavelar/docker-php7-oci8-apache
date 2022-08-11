<?php

namespace backend\controllers;

use Yii;
use backend\models\Servicetaskcustomstates;
use backend\models\ServicetaskcustomstateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * ServicetaskcustomstateController implements the CRUD actions for Servicetaskcustomstates model.
 */
class ServicetaskcustomstateController extends Controller
{
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
     * Deletes an existing Servicetaskcustomstates model.
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
     * Finds the Servicetaskcustomstates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Servicetaskcustomstates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Servicetaskcustomstates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Servicetaskcustomstates();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Servicetaskcustomstates::findOne($criteria);
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if (Yii::$app->request->isAjax){
                $data = Yii::$app->request->post(StringHelper::basename(Servicetaskcustomstates::class));
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                } else {
                    $model = new Servicetaskcustomstates();
                }
                $model->attributes = $data;
                if($model->save()){
                    $response = [
                        'success' => true,
                        'title' => 'Estado Guardado',
                        'message' => 'Estado Guardado Exitosamente',
                    ];
                } else {
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    throw new Exception($message, 92001);
                }
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'errors' => $model->getErrors(),
            ];
        }
        return $response;
    }
}
