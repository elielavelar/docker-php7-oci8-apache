<?php

namespace backend\controllers;

use Yii;
use frontend\models\Citizen;
use backend\models\CitizenSearch;
use common\models\Appointments;
use common\models\AppointmentsSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CitizenController implements the CRUD actions for Citizen model.
 */
class CitizenController extends CustomController
{
    
    public $customactions = [
        
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
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
     * Lists all Citizen models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitizenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Citizen model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Citizen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Citizen();
        $model->scenario = Citizen::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $view = ['index'];
                if(Yii::$app->user->can('citizenUpdate')){
                    $view = ['update', 'id' => $model->Id];
                } elseif(Yii::$app->user->can('citizenView')){
                    $view = ['view', 'id' => $model->Id];
                } 
                return $this->redirect($view);
            } else {
                $this->viewErrors($model->errors);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Citizen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetail = new Appointments();
        $modelDetail->IdCitizen = $model->Id;
        
        $searchModel = new AppointmentsSearch();
        $searchModel->IdCitizen = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                'model' => $model, 'modelDetail'=>$modelDetail, 'searchModel'=>$searchModel,'dataProvider'=>$dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing Citizen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Citizen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Citizen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Citizen::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionSendemailconfirmation($id){
        $model = $this->findModel($id);
        $model->sendEmailConfirmation();
        return $this->redirect(['update', 'id' => $model->Id]);
    }
}
