<?php

namespace backend\controllers;

use Yii;
use backend\models\Infrastructurerequirement;
use backend\models\InfrastructurerequirementSearch;
use backend\models\Infrastructurerequirementdetails;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\State;
use yii\web\Response;
use yii\helpers\Json;
use webtoolsnz\AdminLte\FlashMessage;
use Exception;

/**
 * InfrastructurerequirementController implements the CRUD actions for Infrastructurerequirement model.
 */
class InfrastructurerequirementController extends CustomController
{
    public $customactions = [];
    
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
     * Lists all Infrastructurerequirement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Infrastructurerequirement();
        $searchModel = new InfrastructurerequirementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Infrastructurerequirement model.
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
     * Creates a new Infrastructurerequirement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Infrastructurerequirement();
        $model->TicketDate = Yii::$app->getFormatter()->asDatetime(date('d-m-Y H:i:s'),'php:d-m-Y H:i:s');
        $model->RequirementDate = Yii::$app->getFormatter()->asDatetime(date('d-m-Y H:i:s'),'php:d-m-Y H:i:s');
        $model->IdState = State::findOne(['KeyWord' => StringHelper::basename(Infrastructurerequirement::class),'Code' => Infrastructurerequirement::STATE_PENDENT])->Id;
        $model->IdCreateUser = \Yii::$app->getUser()->getId();
        $model->Quantity = $model::DEFAULT_QUANTITY;
        
        $controller = Yii::$app->controller->id;
        $admin = Yii::$app->customFunctions->userCan($controller.'Admin');
        $create = Yii::$app->customFunctions->userCan($controller.'Create');
        $update = Yii::$app->customFunctions->userCan($controller.'Update');
        $delete = Yii::$app->customFunctions->userCan($controller.'Delete');
        $close = Yii::$app->customFunctions->userCan($controller.'Close');
        $filterDepartment = Yii::$app->customFunctions->userCan($controller.'FilterServiceCentre');
        $permission = [
            'admin' => $admin,
            'create' => $create,
            'update' => $update,
            'delete' => $delete,
            'close' => $close,
            'filterDepartment' => $filterDepartment,
        ];
        $model->IdReportUser = $admin ? null : \Yii::$app->getUser()->getId();
        $model->IdServiceCentre = $filterDepartment ? null : \Yii::$app->getUser()->getIdentity()->IdServiceCentre ;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('message', new FlashMessage([
                    'type' => FlashMessage::TYPE_SUCCESS,
                    'message' => 'Requerimiento creado exitosamente'
            ]));
            return $this->redirect(['update', 'id' => $model->Id]);
        }
//        
//        try {
//            \Yii::$app->telegram->sendMessage([
//                'chat_id'=>'ElementChat',
//                'text' => 'Esto es una Prueba',
//            ]);
//        } catch (Exception $ex) {
//            print_r($ex); die();
//        }
        
        return $this->render('create', [
            'model' => $model, 'permission' => $permission,
        ]);
    }

    /**
     * Updates an existing Infrastructurerequirement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $controller = Yii::$app->controller->id;
        $admin = Yii::$app->customFunctions->userCan($controller.'Admin');
        $create = Yii::$app->customFunctions->userCan($controller.'Create');
        $update = Yii::$app->customFunctions->userCan($controller.'Update');
        $delete = Yii::$app->customFunctions->userCan($controller.'Delete');
        $close = Yii::$app->customFunctions->userCan($controller.'Close');
        $filterDepartment = Yii::$app->customFunctions->userCan($controller.'FilterServiceCentre');
        $permission = [
            'admin' => $admin,
            'create' => $create,
            'update' => $update,
            'delete' => $delete,
            'close' => $close,
            'filterDepartment' => $filterDepartment,
        ];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model, 'permission' => $permission,
        ]);
    }

    /**
     * Deletes an existing Infrastructurerequirement model.
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
     * Finds the Infrastructurerequirement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Infrastructurerequirement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Infrastructurerequirement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Infrastructurerequirement();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Infrastructurerequirement::findOne($criteria);
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