<?php

namespace backend\controllers;

use common\models\Attachment;
use common\models\search\AttachmentSearch;
use Yii;
use backend\models\Incidentrequestdetail;
use backend\models\search\IncidentrequestdetailSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;
use yii\web\UploadedFile;

/**
 * IncidentrequestdetailController implements the CRUD actions for Incidentrequestdetail model.
 */
class IncidentrequestdetailController extends Controller
{
    public $customactions = [
        'getattributelist',
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
     * Lists all Incidentrequestdetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Incidentrequestdetail();
        $searchModel = new IncidentrequestdetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Incidentrequestdetail model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Incidentrequestdetail::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(Incidentrequestdetail::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;
        $attachmentModel->disabled = true;

        return $this->render('view', [
            'model' => $model,
            'searchAttachmentModel' => $searchAttachmentModel,
            'attachmentModel' => $attachmentModel,
            'attachmentDataProvider' => $attachmentDataProvider,
        ]);
    }

    /**
     * Creates a new Incidentrequestdetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Incidentrequestdetail();
        $model->IdIncidentRequest = $id;
        $model->IdUser = Yii::$app->getUser()->getIdentity()->getId();
        $model->IdAssignedUser = $model->IdUser;

        if ($model->load(Yii::$app->request->post())) {
            $model->fileattachment = UploadedFile::getInstances($model, 'fileattachment');
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Incidentrequestdetail model.
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
        $searchAttachmentModel->KeyWord = StringHelper::basename(Incidentrequestdetail::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(Incidentrequestdetail::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;

        return $this->render('update', [
            'model' => $model,
            'searchAttachmentModel' => $searchAttachmentModel,
            'attachmentModel' => $attachmentModel,
            'attachmentDataProvider' => $attachmentDataProvider,
        ]);
    }

    /**
     * Deletes an existing Incidentrequestdetail model.
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
     * Finds the Incidentrequestdetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incidentrequestdetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incidentrequestdetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('system', 'The requested page does not exist.'));
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Incidentrequestdetail();
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, TRUE);
                $model = Incidentrequestdetail::findOne($criteria);
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

    /**
     * @throws HttpException
     */
    public function actionGetattributelist(){
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        try {
            $data = Yii::$app->getRequest()->post();
            $model = new Incidentrequestdetail();
            $model->load($data);
            return ['results' => $model->getAttributesByActivity()];
        } catch ( \Exception $exception){
            throw new HttpException(406, $exception->getMessage(), 406);
        }
    }
}
