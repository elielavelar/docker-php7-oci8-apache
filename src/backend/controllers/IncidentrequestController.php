<?php

namespace backend\controllers;

use backend\models\Incidenttitle;
use Yii;
use backend\models\Incidentrequest;
use backend\models\search\IncidentrequestSearch;
use common\models\State;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use common\models\Attachment;
use common\models\search\AttachmentSearch;
use Exception;
use yii\web\UploadedFile;

/**
 * IncidentrequestController implements the CRUD actions for Incidentrequest model.
 */
class IncidentrequestController extends Controller
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
     * Lists all Incidentrequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Incidentrequest();

        $states = State::getAll(
            StringHelper::basename( Incidentrequest::class ),
            true,
            function( $result ){
                return ArrayHelper::map($result, 'Code', 'Id');
            }
        );

        $searchModelRegistred = new IncidentrequestSearch();
        $searchModelRegistred->IdState = ArrayHelper::getValue( $states, Incidentrequest::STATUS_REGISTRED);
        $dataProviderRegistred = $searchModelRegistred->search(Yii::$app->request->queryParams);

        $searchModelApproved = new IncidentrequestSearch();
        $searchModelApproved->IdState = ArrayHelper::getValue( $states, Incidentrequest::STATUS_APPROVED);
        $dataProviderApproved = $searchModelApproved->search(Yii::$app->request->queryParams);

        $searchModelRejected = new IncidentrequestSearch();
        $searchModelRejected->IdState = ArrayHelper::getValue( $states, Incidentrequest::STATUS_REJECTED);
        $dataProviderRejected = $searchModelRejected->search(Yii::$app->request->queryParams);

        $searchModelProcess = new IncidentrequestSearch();
        $searchModelProcess->IdState = ArrayHelper::getValue( $states, Incidentrequest::STATUS_INPROCESS);
        $dataProviderProcess = $searchModelProcess->search(Yii::$app->request->queryParams);

        $searchModelClosed = new IncidentrequestSearch();
        $searchModelClosed->IdState = ArrayHelper::getValue( $states, Incidentrequest::STATUS_CLOSED);
        $dataProviderClosed = $searchModelClosed->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModelRegistred' => $searchModelRegistred,
            'dataProviderRegistred' => $dataProviderRegistred,
            'searchModelApproved' => $searchModelApproved,
            'dataProviderApproved' => $dataProviderApproved,
            'searchModelRejected' => $searchModelApproved,
            'dataProviderRejected' => $dataProviderApproved,
            'searchModelProcess' => $searchModelProcess,
            'dataProviderProcess' => $dataProviderProcess,
            'searchModelClosed' => $searchModelClosed,
            'dataProviderClosed' => $dataProviderClosed,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Incidentrequest model.
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
     * Creates a new Incidentrequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Incidentrequest();
        $user = Yii::$app->getUser()->getIdentity();
        $model->IdUser = $user->Id;
        $model->IdCreateUser = $user->Id;
        $filterDepartments = Yii::$app->customFunctions->userCan($this->id.'FilterDept');
        $model->IdServiceCentre = $user->IdServiceCentre;

        if ($model->load(Yii::$app->request->post())) {
            $model->fileattachment = UploadedFile::getInstances($model, 'fileattachment');
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        }
        $modelTitle = new Incidenttitle();

        return $this->render('create', [
            'model' => $model,
            'filterDepartments' => $filterDepartments,
            'modelTitle' => $modelTitle
        ]);
    }

    /**
     * Updates an existing Incidentrequest model.
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

        $modelTitle = new Incidenttitle();

        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Incidentrequest::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(Incidentrequest::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;

        $filterDepartments = Yii::$app->customFunctions->userCan($this->id.'FilterDept');
        return $this->render('update', [
            'model' => $model,
            'filterDepartments'=> $filterDepartments,
            'attachmentModel' => $attachmentModel,
            'searchAttachmentModel' => $searchAttachmentModel,
            'attachmentDataProvider' => $attachmentDataProvider,
            'modelTitle' => $modelTitle,
        ]);
    }

    /**
     * Deletes an existing Incidentrequest model.
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
     * Finds the Incidentrequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incidentrequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incidentrequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('system', 'The requested page does not exist.'));
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Incidentrequest();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Incidentrequest::findOne($criteria);
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
