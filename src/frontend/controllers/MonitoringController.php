<?php

namespace frontend\controllers;

use Yii;
use common\models\Servicecentre;
use backend\models\Servicetask;
use backend\models\ServicetaskSearch;
use backend\models\Setting;
use backend\models\Settingdetail;
use common\models\State;
use common\models\Type;
use frontend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
/**
 * ServicetaskController implements the CRUD actions for Servicetask model.
 */
class MonitoringController extends Custom\Controller
{
    public $customactions = [
        'save', 'get', 'getdata',
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
     * Lists all Servicecentre models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Servicecentre();
        $searchModel = Servicecentre::find()
                ->joinWith('state b')
                ->joinWith('type c')
                ->innerJoin(Settingdetail::tableName().' d','d.Code = c.Code')
                ->innerJoin(Setting::tableName().' e','e.Id = d.IdSetting')
                ->innerJoin(State::tableName().' f','f.Id = d.IdState')
                ->where([
                    'b.Code' => Servicecentre::STATE_ACTIVE,
                    'e.KeyWord' => StringHelper::basename(Servicecentre::class),
                    'e.Code' => 'MON',
                    'f.Code' => Setting::STATUS_ACTIVE,
                    Servicecentre::tableName().'.EnabledMonitoring' => Servicecentre::MONITORING_ENABLED,
                ])
                #->andWhere('(c.Code IN(:duisite, :datacenter))',[':duisite' => Servicecentres::TYPE_DUISITE,':datacenter' => Servicecentres::TYPE_DATACENTER])
                ->orderBy([Servicecentre::tableName().'.Name' => SORT_ASC])
                ->all();
        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionSave(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if (Yii::$app->request->isAjax){
                $data = Yii::$app->request->post(StringHelper::basename(Servicetask::class));
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                } else {
                    $model = new Servicetask();
                }
                $model->attributes = $data;
                if($model->save()){
                    $response = [
                        'success' => true,
                        'title' => 'Tarea Guardada',
                        'message' => 'Tarea Guardada Exitosamente',
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
    /**
     * Deletes an existing Servicetask model.
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
     * Finds the Servicetask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Servicetask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Servicetask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if(Yii::$app->request->isAjax){
                $input = Yii::$app->request->post('data');
                $data = Json::decode($input, true);
                $model = Servicecentre::findOne(['Id' => $data['Id']]);
                if(!empty($model)){
                    $response = $model->getServicesStatus();
                    $response['success'] = true;
                } else {
                    throw new Exception('Registro no encontrado',92099);
                }
            }
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'errors' => $model->hasErrors() ? $model->errors : [],
            ];
        }
        return $response;
    }
    
    public function actionGetdata(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            if(Yii::$app->request->isAjax){
                $input = Yii::$app->request->post('data');
                $data = Json::decode($input, true);
                $model = new Servicecentre();
                $response = $model->getDataServicesStatus();
            }
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ];
        }
        return $response;
    }
}
