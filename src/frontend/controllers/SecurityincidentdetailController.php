<?php

namespace frontend\controllers;

use Yii;
use backend\models\Securityincidentdetails;
use backend\models\SecurityincidentdetailSearch;
use backend\models\Attachments;
use backend\models\AttachmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\Json;
use yii\web\Response;
use Exception;

/**
 * SecurityincidentdetailController implements the CRUD actions for Securityincidentdetails model.
 */
class SecurityincidentdetailController extends Controller
{
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
     * Lists all Securityincidentdetails models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SecurityincidentdetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Securityincidentdetails model.
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
     * Creates a new Securityincidentdetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Securityincidentdetails();
        $model->IdSecurityIncident = $id;
        $model->DetailDate = Yii::$app->getFormatter()->asDate(date('d-m-Y H:i'),'php:d-m-Y H:i');
        $model->RecordDate = Yii::$app->getFormatter()->asDate(date('d-m-Y H:i'),'php:d-m-Y H:i');
        $model->IdActivityType = Type::findOne(['KeyWord'=> StringHelper::basename(Securityincidentdetails::class).'Activity', 'Code'=>Securityincidentdetails::ACTIVITY_FOLLOWING])->Id;
        $model->IdUser = Yii::$app->user->getIdentity()->getId();
        $model->assignDefaultUser();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Securityincidentdetails model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(Securityincidentdetails::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);
        
        $attachmentModel = new Attachments();
        $attachmentModel->KeyWord = StringHelper::basename(Securityincidentdetails::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        }

        return $this->render('update', [
            'model' => $model
                , 'searchModel' => $searchAttachmentModel
                , 'dataProvider' => $attachmentDataProvider
                , 'modelDetail' => $attachmentModel
        ]);
    }

    /**
     * Deletes an existing Securityincidentdetails model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = $this->findModel($id);
            $title = 'Detalle';
            $name = '';
            $dttitle = 'Eliminado';
            if($model->delete()){
                $response = [
                    'success'=>TRUE,
                    'message'=>$title.' '.$name.' '.$dttitle,
                    'title'=>$title.' '.$dttitle,
                ];
            } else {
                $message = Yii::$app->customFunctions->getErrors($model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex){
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Securityincidentdetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Securityincidentdetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Securityincidentdetails::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
}
