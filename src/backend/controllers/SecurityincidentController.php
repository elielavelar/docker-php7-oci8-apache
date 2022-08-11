<?php

namespace backend\controllers;

use Yii;
use backend\models\Securityincident;
use backend\models\SecurityincidentSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\State;
use common\models\Type;
use backend\models\Incident;
use backend\models\Securityincidentdetails;
use backend\models\SecurityincidentdetailSearch;
use backend\models\Attachments;
use backend\models\AttachmentSearch;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * SecurityincidentController implements the CRUD actions for Securityincident model.
 */
class SecurityincidentController extends CustomController
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
     * Lists all Securityincident models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Securityincident();
        $model->Year = date('Y');
        $searchModel = new SecurityincidentSearch();
        $params = Yii::$app->request->queryParams;
        if(empty($params)){
            $searchModel->Year = $model->Year;
        }
        $dataProvider = $searchModel->search($params);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Securityincident model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        $model = $this->findModel($id);
        
        $controller = Yii::$app->controller->id;
        $admin = Yii::$app->customFunctions->userCan($controller.'Admin');
        $create = Yii::$app->customFunctions->userCan($controller.'Create');
        $update = Yii::$app->customFunctions->userCan($controller.'Update');
        $delete = Yii::$app->customFunctions->userCan($controller.'Delete');
        $view = Yii::$app->customFunctions->userCan($controller.'View');
        $close= Yii::$app->customFunctions->userCan($controller.'Close');
        
        $searchModel = new SecurityincidentdetailSearch();
        $searchModel->IdSecurityIncident = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $modelDetail = new Securityincidentdetails();
        $modelDetail->IdSecurityIncident = $model->Id;
        
        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Securityincident::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);
        
        $attachmentModel = new Attachments();
        $attachmentModel->KeyWord = StringHelper::basename(Securityincident::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;
        
        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel, 'modelDetail' => $modelDetail, 
            'searchAttachmentModel' => $searchAttachmentModel, 'attachmentDataProvider' => $attachmentDataProvider,
            'attachmentModel' => $attachmentModel, 'dataProvider' => $dataProvider,
            'admin' => $admin, 'create' => $create, 'update' => $update, 'view' => $view, 'close' => $close, 'delete' => $delete
        ]);
    }

    /**
     * Creates a new Securityincident model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Securityincident();
        $model->TicketDate = Yii::$app->getFormatter()->asDatetime(date('d-m-Y H:i:s'),'php:d-m-Y H:i:s');
        $model->IncidentDate = Yii::$app->getFormatter()->asDatetime(date('d-m-Y H:i:s'),'php:d-m-Y H:i:s');
        $model->IdState = State::findOne(['KeyWord'=> StringHelper::basename(Securityincident::class),'Code'=> Securityincident::STATE_REGISTRED])->Id;
        $model->IdCreateUser = Yii::$app->user->getIdentity()->getId();
        $model->IdReportUser = Yii::$app->user->getIdentity()->getId();
        $model->IdLevelType = Type::findOne(['KeyWord' => StringHelper::basename(Securityincident::class).'Level', 'Code' => Securityincident::LEVEL_WITHOUT_RISK])->Id;
        $model->IdPriorityType = Type::findOne(['KeyWord' => StringHelper::basename(Incident::class).'Priority', 'Code' => Incident::PRIORITY_LOW])->Id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Securityincident model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $searchModel = new SecurityincidentdetailSearch();
        $searchModel->IdSecurityIncident = $model->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $modelDetail = new Securityincidentdetails();
        $modelDetail->IdSecurityIncident = $model->Id;
        
        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Securityincident::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);
        
        $attachmentModel = new Attachments();
        $attachmentModel->KeyWord = StringHelper::basename(Securityincident::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model, 'searchModel' => $searchModel
                , 'dataProvider' => $dataProvider, 'modelDetail' => $modelDetail
                , 'searchAttachmentModel' => $searchAttachmentModel
                , 'attachmentDataProvider' => $attachmentDataProvider
                , 'attachmentModel' => $attachmentModel,
        ]);
    }

    /**
     * Deletes an existing Securityincident model.
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
     * Finds the Securityincident model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Securityincident the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Securityincident::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
