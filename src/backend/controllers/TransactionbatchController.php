<?php

namespace backend\controllers;

use Yii;
use common\models\Transactionbatch;
use backend\models\TransactionbatchSearch;
use common\models\Transaction;
use backend\models\TransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

use yii\web\UploadedFile;

/**
 * TransactionbatchController implements the CRUD actions for Transactionbatch model.
 */
class TransactionbatchController extends Controller
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
     * Lists all Transactionbatch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Transactionbatch();
        $searchModel = new TransactionbatchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'model'=> $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transactionbatch model.
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
     * Creates a new Transactionbatch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transactionbatch();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Transactionbatch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetail = new Transaction();
        $modelDetail->IdTransactionBatch = $model->Id;
        
        $searchModel = new TransactionSearch();
        $searchModel->IdTransactionBatch = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model, 'modelDetail'=> $modelDetail
                , 'searchModel'=> $searchModel, 'dataProvider'=> $dataProvider,
        ]);
    }
    
    public function actionGenerate(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            $input = Yii::$app->request->post('data');
            $data = Json::decode($input, TRUE);
            $model = $this->findModel($data['id']);
            $batch = $model->generateXMLFile(TRUE);
            $response = [
                'success'=> TRUE,
                'message'=> 'Respaldo Generado Exitosamente',
                'url'=> $batch,
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionUpload(){
        try {
            $model = new Transactionbatch();
            $model->scenario = Transactionbatch::SCENARIO_UPLOAD;
            if (Yii::$app->request->isPost) {
                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
                if($model->upload()){
                    Yii::$app->session->setFlash('success', 'Respaldo Cargado Exitosamente');
                } else {
                    $this->viewErrors($model->errors);
                }
            }
            
            $this->redirect(['index']);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }



    public function actionLoadbatch(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            $input = Yii::$app->request->post('data');
            $criteria = Json::decode($input, TRUE);
            
            $model = Transactionbatch::find()->where($criteria)->one();
            if($model){
                
            } else {
                throw new Exception('ERROR: Registro no encontrado', 91000);
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }


    public function actionApply(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            $input = Yii::$app->request->post('data');
            $criteria = Json::decode($input, TRUE);
            
            $model = Transactionbatch::find()->where($criteria)->one();
            if($model){
                $model->applyBatch();
            } else {
                throw new Exception('ERROR: Lote no encontrado', 91000);
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }

    /**
     * Deletes an existing Transactionbatch model.
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
     * Finds the Transactionbatch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Transactionbatch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transactionbatch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
