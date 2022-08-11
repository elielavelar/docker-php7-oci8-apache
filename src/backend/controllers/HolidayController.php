<?php

namespace backend\controllers;

use Yii;
use common\models\Holidays;
use backend\models\HolidaySearch;
use common\models\Holidaysdetails;
use common\models\Servicecentres;
use yii\db\Query;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;

/**
 * HolidayController implements the CRUD actions for Holidays model.
 */
class HolidayController extends CustomController
{
    
    public $customactions = [
        'get'
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
     * Lists all Holidays models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HolidaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Holidays model.
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
     * Creates a new Holidays model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Holidays();

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                return $this->redirect(['update', 'id' => $model->Id]);
            } else {
                \Yii::$app->session->setFlash('error', \Yii::$app->customFunctions->getErrors($model->errors));
                echo '<pre>';
                print_r($model->attributes); die();
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
     * Updates an existing Holidays model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetails = NULL;
        $checkedItems = [];
        if ($model->Id != NULL){
            $details = Servicecentres::find()
                    ->select(['servicecentres.Id','servicecentres.MBCode','servicecentres.Name','servicecentres.IdType','c.IdHoliday'])
                    ->joinWith('type b')
                    ->leftJoin('holidaysdetails c','c.IdHoliday =:idHoliday and c.IdServiceCentre = servicecentres.Id',[':idHoliday'=> $model->Id])
                    ->where(['b.Code' => Servicecentres::TYPE_DUISITE])
                    ->orderBy(['servicecentres.Id'=>'ASC'])
                    ->asArray()
                    ->all();
            $modelDetails = [];
            
            foreach ($details as $det){
                $modelDetails[$det["Id"]] = $det["Name"];
                if(!empty($det["IdHoliday"])){
                    array_push($checkedItems, $det["Id"])  ;
                } 
            }
            $model->holidaysitems = $checkedItems;

        }
        
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(Holidays::className()));
            if(isset($post["holidaysitems"])){
                $model->holidaysitems = $post["holidaysitems"];
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                'model' => $model, 'modelDetails' => $modelDetails,
            ]);
        }
    }

    /**
     * Deletes an existing Holidays model.
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
     * Finds the Holidays model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Holidays the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Holidays::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
