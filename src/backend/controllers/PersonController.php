<?php

namespace backend\controllers;

use Yii;
use common\models\Personaldocument;
use common\models\search\PersonaldocumentSearch;
use common\models\Person;
use common\models\search\PersonSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends CustomController
{
    public $customactions = [];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }
    
    public function __construct($id, $module, $config = array()) {
        //$this->defaultModel = (Person::instance())->getSystemModel();
        return parent::__construct($id, $module, $config);
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
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Person();
        $searchModel = new PersonSearch();
        $params = isset(Yii::$app->request->queryParams[StringHelper::basename(PersonSearch::class)]) ? Yii::$app->request->queryParams[StringHelper::basename(PersonSearch::class)] : [];
        $searchModel->Id = !array_filter($params) ? 0 : null;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $modelDetail = new Personaldocument();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'modelDetail' => $modelDetail,
        ]);
    }

    /**
     * Displays a single Person model.
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
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Person();
        //echo '<pre>';
        //print_r($model); die();
        $modelDetail = new Personaldocument();
        $modelDetail->IdPerson = $model->Id;
        $searchModel = new PersonaldocumentSearch();
        $searchModel->IdPerson = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(Person::class));
            $documents = isset($post[StringHelper::basename(Personaldocument::class)]) ? $post[StringHelper::basename(Personaldocument::class)]: [];
            $model->setDocuments($documents);
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelDetail' => $modelDetail,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Person model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetail = new Personaldocument();
        $modelDetail->IdPerson = $model->Id;
        $searchModel = new PersonaldocumentSearch();
        $searchModel->IdPerson = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(Person::class));
            $documents = isset($post[StringHelper::basename(Personaldocument::class)]) ? $post[StringHelper::basename(Personaldocument::class)]: [];
            $model->setDocuments($documents);
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelDetail' => $modelDetail,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Person model.
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
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Person();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Person::findOne($criteria);
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

    public function actionGetdocumentfield(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Person();
        $response = [];
        try {
            $data = Yii::$app->request->post(StringHelper::basename(Person::class));
            if(Yii::$app->request->isAjax){
                $response = [
                    'success' => true,
                    'field' => $model->getTempDocumentField(),
                ];
            }
        } catch (\Throwable $th){
            $response = [
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'errors' => $model->hasErrors() ? $model->errors : [],
            ];
        }
        return $response;
    }
}
