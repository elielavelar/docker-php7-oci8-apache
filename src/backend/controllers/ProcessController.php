<?php

namespace backend\controllers;

use Yii;
use backend\models\Process;
use backend\models\ProcessSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Processdetail;
use backend\models\ProcessdetailSearch;
use common\models\Servicecentres;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * ProcessController implements the CRUD actions for Process model.
 */
class ProcessController extends Controller
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
     * Lists all Process models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Process model.
     * @param integer $id
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
     * Creates a new Process model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Process();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Process model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetail = NULL;
        $checkedItems = [];
        if ($model->Id != NULL){
            $details = Servicecentres::find()
                    ->select(['servicecentres.Id','servicecentres.MBCode','servicecentres.Name','servicecentres.IdType','c.IdProcess'])
                    ->joinWith('type b')
                    ->leftJoin('processdetail c','c.IdProcess =:idProcess and c.IdServiceCentre = servicecentres.Id',[':idProcess'=> $model->Id])
                    ->where('b.Code != :code',[':code' => Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.Id'=>'ASC'])
                    ->asArray()
                    ->all();
            $modelDetail = [];
            
            foreach ($details as $det){
                $modelDetail[$det["Id"]] = $det["Name"];
                if(!empty($det["IdProcess"])){
                    array_push($checkedItems, $det["Id"])  ;
                } 
            }
            $model->processitems = $checkedItems;

        }
        
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(Process::className()));
            if(isset($post["processitems"])){
                $model->processitems = $post["processitems"];
            }
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model, 'modelDetail' => $modelDetail
        ]);
    }

    /**
     * Deletes an existing Process model.
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
     * Finds the Process model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Process the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Process::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
