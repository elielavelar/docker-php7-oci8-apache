<?php

namespace backend\controllers;

use Yii;
use common\models\Fieldcatalogsource;
use backend\controllers\BasicController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

class FieldcatalogmodelsourceController extends BasicController {
    //put your code here
    
    public function actionGetmodelattributesform(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Fieldcatalogsource();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, true);
                $model = new Fieldcatalogsource();
                $model->IdRegistredModel = $criteria['Id'];
                $model->IdField = $criteria['IdField'];
                $model->lastId = isset($criteria['lastId']) ? $criteria['lastId'] : null;
                $response = ['success' => true, 'container' =>  $model->getModelAttributesForm()];
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
