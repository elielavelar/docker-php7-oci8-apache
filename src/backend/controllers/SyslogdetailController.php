<?php

namespace backend\controllers;

use Yii;
use common\models\Syslogdetail;
use backend\models\SyslogdetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * SyslogdetailController implements the CRUD actions for Syslogdetail model.
 */
class SyslogdetailController extends Controller
{
    public function actionGet(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = Json::decode($data, TRUE);
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
                'errors' => $model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Syslogdetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Syslogdetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Syslogdetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
