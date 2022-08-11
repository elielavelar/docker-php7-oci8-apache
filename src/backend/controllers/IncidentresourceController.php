<?php

namespace backend\controllers;

use common\models\Resource;
use Yii;
use backend\models\Incidentresource;
use backend\models\search\IncidentresourceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * IncidentresourceController implements the CRUD actions for Incidentresource model.
 */
class IncidentresourceController extends CustomController
{
    public $customactions = [
        'getlistbyresourcetype'
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
     * Finds the Incidentresource model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incidentresource the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incidentresource::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('system', 'The requested page does not exist.'));
    }

    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Incidentresource();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Incidentresource::findOne($criteria);
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

    public function actionGetlistbyresourcetype(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results'=> ['id'=> '','text'=> '']
        ];
        $data = Yii::$app->getRequest()->getQueryParams();
        $result['results']= Resource::getCategoryList($q, $idparent);
        try {

        } catch(Exception $exception){

        }
        return $result;
    }
}
