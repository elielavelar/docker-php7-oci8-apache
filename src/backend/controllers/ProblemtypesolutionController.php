<?php

namespace backend\controllers;

use Yii;
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

/**
 * ProblemtypesolutionController implements the CRUD actions for Problemtypesolution model.
 */
class ProblemtypesolutionController extends CustomController
{
    public $customactions = [];
    
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Deletes an existing Problemtypesolution model.
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
     * Finds the Problemtypesolution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Problemtypesolution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Problemtypesolution::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('system', 'The requested page does not exist.'));
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Problemtypesolution();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Problemtypesolution::findOne($criteria);
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
        $model = new Problemtypesolution();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post(StringHelper::basename(Problemtypesolution::class));
                $id = ArrayHelper::getValue($data, 'Id');
                $dttitle = 'Agregado';
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
                    $title = Yii::t('system','Solution');
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
}
