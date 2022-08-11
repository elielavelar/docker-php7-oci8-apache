<?php

namespace backend\controllers;

use common\models\Attachment;
use common\models\search\AttachmentSearch;
use Yii;
use backend\models\Incidentdetail;
use backend\models\IncidentdetailSearch;
use backend\controllers\CustomController;
use yii\helpers\StringHelper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * IncidentdetailController implements the CRUD actions for Incidentdetail model.
 */
class IncidentdetailController extends CustomController
{
    public $customactions = [
        'get', 'getattributelist',
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
     * Displays a single Incidentdetail model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', $this->_getIncidentdetailModels($model));
    }

    /**
     * Creates a new Incidentdetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Incidentdetail();
        $model->IdIncident = $id;
        $model->IdUser = Yii::$app->getUser()->getIdentity()->Id;
        $model->IdAssignedUser = $model->IdUser;
        $model->IdCategoryType = $model->incident->IdCategoryType;
        $model->IdSubCategoryType = $model->incident->IdSubCategoryType;

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
     * Updates an existing Incidentdetail model.
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

        return $this->render('update', $this->_getIncidentdetailModels($model));
    }

    /**
     * Deletes an existing Incidentdetail model.
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
     * Finds the Incidentdetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incidentdetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incidentdetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws HttpException
     */
    public function actionGetattributelist(){
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        try {
            $data = Yii::$app->getRequest()->post();
            $model = new Incidentdetail();
            $model->load($data);
            $model->IdCategoryType = empty( $model->IdCategoryType )
                ? ArrayHelper::getValue($data, 'iddefaultcategory', null)
                : $model->IdCategoryType;
            $model->IdSubCategoryType = empty( $model->IdSubCategoryType )
                ? ArrayHelper::getValue($data, 'iddefaultsubcategory', null)
                : $model->IdSubCategoryType;
            return ['results' => $model->getAttributesByActivity()];
        } catch ( \Exception $exception){
            throw new HttpException(406, $exception->getMessage(), 406);
        }
    }

    private function _getIncidentdetailModels( $model ){

        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Incidentdetail::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(Incidentdetail::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;
        return [
            'model' => $model,
            'attachmentModel' => $attachmentModel,
            'searchAttachmentModel' => $searchAttachmentModel,
            'attachmentDataProvider' => $attachmentDataProvider,
        ];
    }
}
