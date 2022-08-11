<?php

namespace backend\controllers;

use Yii;
use common\models\Field;
use common\models\search\FieldSearch;
use common\models\Fieldcatalogsource;
use common\models\Fieldcatalog;
use common\models\search\FieldcatalogSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

/**
 * FieldController implements the CRUD actions for Fields model.
 */
class FieldController extends CustomController
{
    
    public $customactions = [
        'get','getmodelattributesform'
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
     * Lists all Fields models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Field();
        $searchModel = new FieldSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Field model.
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
     * Creates a new Fields model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Field();
        $model->UseMask = $model::USE_MASK_DISABLED;
        $model->HasCatalog = $model::HAS_CATALOG_FALSE;
        $model->EnabledCustomMask = $model::CUSTOM_MASK_DISABLED;
        $model->MultipleValue = $model::MULTIPLE_VALUES_FALSE;
        $model->EnabledModelSource = $model::MODEL_SOURCE_DISABLED;
        
        $modelSource = new Fieldcatalogsource();
        $modelSource->IdField = $model->Id;

        if($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post(StringHelper::basename(Field::class));
            $conditions = isset($data['condition']) ? $data['condition'] : [];
            $model->setCondition($conditions);
            $model->customvalue = isset($data['customvalue']) ? $data['customvalue'] : null;
            if($model->save()){
                return $this->redirect(['update', 'id' => $model->Id]);
            } 
        }

        return $this->render('create', [
            'model' => $model, 
            'modelSource' => $modelSource,
        ]);
    }

    /**
     * Updates an existing Fields model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->EnabledModelSource ? $model->getModelAttributes() : null;
        $searchModel = new FieldcatalogSearch();
        $searchModel->IdField = $model->Id;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        $modelSource = new Fieldcatalogsource();
        $modelSource->IdField = $model->Id;
        
        $modelDetail = new Fieldcatalog();
        $modelDetail->IdField = $model->Id;
        $modelDetail->Sort = $modelDetail::SORT_DEFAULT_VALUE;

        if($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post(StringHelper::basename(Field::class));
            $conditions = isset($data['condition']) ? $data['condition'] : [];
            $model->setCondition($conditions);
            $model->customvalue = isset($data['customvalue']) ? $data['customvalue'] : null;
            if($model->save()){
                $this->setFlashMessage('Registro Actualizado', 'Campo Actualizado Correctamente', parent::ALERT_TYPE_SUCCESS);
                return $this->redirect(['update', 'id' => $model->Id]);
            } 
        }
        if($model->EnabledModelSource){
            $model->conditionform = $model->loadModelAttributesForm();
        }

        return $this->render('update', [
            'model' => $model, 
            'searchModel' => $searchModel,
            'modelDetail' => $modelDetail,
            'dataProvider' => $dataProvider,
            'modelSource' => $modelSource,
        ]);
    }

    /**
     * Deletes an existing Fields model.
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
     * Finds the Fields model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fields the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Field::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Fields();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Field::findOne($criteria);
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
    
}
