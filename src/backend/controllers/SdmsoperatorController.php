<?php

namespace backend\controllers;

use Yii;
use backend\models\sdms\DatosOper;
use backend\models\sdms\DatosoperSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use Exception;

class SdmsoperatorController extends CustomController
{
    public $customactions = [
        'getcode', 'get' , 'getrandompass', 
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
     * Lists all DatosOper models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DatosoperSearch();
        $searchModel->STAT_OPER = DatosOper::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DatosOper model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DatosOper model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DatosOper();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->COD_OPER]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DatosOper model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->COD_OPER]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DatosOper model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DatosOper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DatosOper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DatosOper::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGetcode(){
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = new DatosOper();
            if (Yii::$app->request->isAjax) {
                $input = Yii::$app->request->post('data');
                $data = Json::decode($input, TRUE);
                $model->attributes = $data;
                $model->getCode();
                $response = [
                    'success' => true,
                ];
                $response = array_merge($response, $model->attributes);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
                'errors' => $model->errors,
            ];
        }
        return $response;
    }
    
    public function actionGetrandompass(){
        $response = [
            'success'=> FALSE,
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $response = [
                'success'=> TRUE,
                #'password'=> \Yii::$app->customFunctions->getRandomPass(NULL, 12),
                'password'=> \Yii::$app->getSecurity()->generateRandomString(8),
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionUpdatepassword(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = Yii::$app->request->post(StringHelper::basename(DatosOper::class));
            $model = $this->findModel($data['COD_OPER']);
            $model->attributes = $data;
            if($model->save()){
                $response = [
                    'success' => TRUE,
                    'message' => 'Contraseña de Operador Actualizada Exitosamente',
                    'title' => 'Operador Actualizado',
                ] ;
            } else {
                $message = Yii::$app->customFunctions->getErrors($model->errors);
                throw new Exception($message, 99000);
            }
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'errors' => $model->getErrors(),
            ];
        }
        return $response;
    }
}
