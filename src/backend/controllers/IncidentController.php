<?php

namespace backend\controllers;

use backend\models\Incidentdetail;
use backend\models\IncidentdetailSearch;
use backend\models\Incidentresource;
use backend\models\Incidenttitle;
use backend\models\search\IncidentresourceSearch;
use Yii;
use backend\models\Incident;
use backend\models\IncidentSearch;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

use common\models\State;
use common\models\Type;
use common\models\Attachment;
use common\models\search\AttachmentSearch;
use yii\web\UploadedFile;


/**
 * IncidentController implements the CRUD actions for Incident model.
 */
class IncidentController extends CustomController
{
    
    public $customactions = [
        'get',
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Incident models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModelAssigned = new IncidentSearch();
        $searchModelAssigned->IdState = State::get(
                    StringHelper::basename(Incident::class),
                Incident::STATE_ASSIGNED,
                    true,
                        function( $result ){
                            return ArrayHelper::getValue($result, 'Id');
                        }
                    );
        $dataProviderAssigned = $searchModelAssigned->search(Yii::$app->request->queryParams);
        
        $searchModelInProcess = new IncidentSearch();
        $searchModelInProcess->IdState = State::get(
            StringHelper::basename(Incident::class),
            Incident::STATE_INPROCESS,
            true,
            function( $result ){
                return ArrayHelper::getValue($result, 'Id');
            }
        );
        $dataProviderInProcess = $searchModelInProcess->search([]);
        
        $searchModelSolved = new IncidentSearch();
        $searchModelSolved->IdState = State::get(
            StringHelper::basename(Incident::class),
            Incident::STATE_SOLVED,
            true,
            function( $result ){
                return ArrayHelper::getValue($result, 'Id');
            }
        );
        $dataProviderSolved = $searchModelSolved->search(Yii::$app->request->queryParams);
        
        $searchModelClosed= new IncidentSearch();
        $searchModelClosed->IdState = State::get(
            StringHelper::basename(Incident::class),
            Incident::STATE_CLOSED,
            true,
            function( $result ){
                return ArrayHelper::getValue($result, 'Id');
            }
        );
        $dataProviderClosed = $searchModelClosed->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModelAssigned' => $searchModelAssigned,
            'dataProviderAssigned' => $dataProviderAssigned,
            'searchModelInProcess' => $searchModelInProcess,
            'dataProviderInProcess' => $dataProviderInProcess,
            'searchModelSolved' => $searchModelSolved,
            'dataProviderSolved' => $dataProviderSolved,
            'searchModelClosed' => $searchModelClosed,
            'dataProviderClosed' => $dataProviderClosed,
        ]);
    }

    /**
     * Displays a single Incident model.
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
     * Creates a new Incident model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Incident();
        $model->setScenario( Incident::SCENARIO_CREATE);
        $model->TicketDate = Yii::$app->getFormatter()->asDate(date('d-m-Y H:i'),'php:d-m-Y H:i');
        $model->IncidentDate = Yii::$app->getFormatter()->asDatetime(date('d-m-Y H:i'),'php:d-m-Y H:i');
        $model->IdState = State::findOne(['KeyWord'=> StringHelper::basename(Incident::class),'Code'=> Incident::STATE_OPENED])->Id;
        $model->IdCreateUser = Yii::$app->user->getIdentity()->getId();
        $model->userName = $model->createUser->DisplayName;
        $model->IdReportUser = Yii::$app->user->getIdentity()->getId();
        $model->IdPriorityType = Type::findOne(['KeyWord'=> StringHelper::basename(Incident::class)."Priority",'Code'=> Incident::PRIORITY_LOW])->Id;
        $filterDepartments = Yii::$app->customFunctions->userCan('filterServicecentrres');

        $model->IdServiceCentre = $model->createUser->IdServiceCentre;
        $model->IdUser = Yii::$app->user->getIdentity()->getId();

        $modelTitle = new Incidenttitle();

        if ($model->load(Yii::$app->request->post())) {
            $model->fileattachment = UploadedFile::getInstances($model, 'fileattachment');
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        }

        return $this->render('create', [
            'model' => $model, 'filterDepartments'=> $filterDepartments, 'modelTitle' => $modelTitle
        ]);
    }

    /**
     * Updates an existing Incident model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Incident::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(Incident::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;

        $resourceModel = new Incidentresource();
        $resourceModel->IdIncident = $model->Id;
        $searchResourceModel = new IncidentresourceSearch();
        $searchResourceModel->IdIncident = $model->Id;
        $resourceDataProvider = $searchResourceModel->search( Yii::$app->request->queryParams);
        $filterDepartments = Yii::$app->customFunctions->userCan($this->id.'FilterDepartments');

        $modelTitle = new Incidenttitle();
        return $this->render('update', [
            'model' => $model, 'filterDepartments'=> $filterDepartments,
            'attachmentModel' => $attachmentModel,
            'searchAttachmentModel' => $searchAttachmentModel,
            'attachmentDataProvider' => $attachmentDataProvider,
            'resourceModel' => $resourceModel,
            'searchResourceModel' => $searchResourceModel,
            'resourceDataProvider' => $resourceDataProvider,
            'modelTitle' => $modelTitle,
        ]);
    }

    /**
     * Deletes an existing Incident model.
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
     * Finds the Incident model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incident the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incident::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
