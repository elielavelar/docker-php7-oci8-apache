<?php

namespace frontend\controllers;

use Yii;
use common\models\Appointments;
use common\models\State;
use common\models\AppointmentsSearch;
use frontend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use Exception;
use yii\helpers\Json;
use yii\helpers\Url;

ini_set('max_execution_time', 600);
/**
 * AppointmentController implements the CRUD actions for Appointments model.
 */
class AppointmentController extends CustomController
{
    
    
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
     * Lists all Appointments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Appointments();
        $model->IdCitizen = \Yii::$app->user->getIdentity()->getId();
        $action = '';
        
        $searchModel = new AppointmentsSearch();
        $searchModel->IdCitizen = $model->IdCitizen;
        $searchModel->IdState = State::findOne(['KeyWord'=>'Appointments','Code'=>'ACT'])->Id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if($data = Yii::$app->request->post('Appointments')){
            if(!empty($data["Id"])){
                $model = $this->findModel($data["Id"]);
                $action = 'update';
            } else {
                $action = 'create';
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            
            $state = State::findOne(['KeyWord'=> StringHelper::basename(Appointments::className()),'Code'=>  Appointments::ACTIVE_STATUS]);
            $model->IdState = $state->Id;
            #print_r($model->attributes); die();
            if($model->save()){
                #print_r($model->attributes); die();
                $this->sendConfirmationMail($model, $action);
                $model = new Appointments();
                $model->IdCitizen = \Yii::$app->user->getIdentity()->getId();
                Yii::$app->session->setFlash('success', 'Cita agendada Exitosamente');
            } else {
                $this->viewErrors($model->errors);
            }
            
            
        } else {
            $this->viewErrors($model->errors);
        }
        
        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Appointments model.
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
     * Creates a new Appointments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Appointments();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->sendConfirmationMail($model, $this->action->id);
            //return $this->redirect(['view', 'id' => $model->Id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Appointments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($model->idCitizen->Email){
                $this->sendConfirmationMail($model, $this->action->id);
            }
            //return $this->redirect(['view', 'id' => $model->Id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Appointments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {   
        $model = $this->findModel($id);
        if($model->delete()){
            Yii::$app->session->setFlash('warning', 'Cita eliminada Exitosamente');
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionCancel($id){
        $model = $this->findModel($id);
        $model->scenario = Appointments::SCENARIO_CANCEL;
        if($model->cancel()){
            $this->sendConfirmationMail($model, $this->action->id);
            Yii::$app->session->setFlash('warning', 'Cita cancelada Exitosamente');
        } else {
            if(!empty($model->errors)){
                foreach ($model->errors as $error){
                    $message = (implode("- ", $error));
                    Yii::$app->session->setFlash('error', $message);
                }
            }
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionGet(){
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = json_decode($data, TRUE);
                $model = $this->findModel($data['id']);
                if($model == NULL){
                    throw new Exception('No se encontró registro', 90001);
                }
                $response = array_merge(['success'=>true],$model->attributes);
                $response = array_merge($response, ['day'=>$model->_day]);
                
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
            ];
        }
        echo json_encode($response);
    }
    
    public function actionGethours(){
        $response = [];
        $model = new Appointments();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = \Yii::$app->request->post('data');
            $data = Json::decode($data, TRUE);
            $model->attributes = $data;
            if(!$model->IdServiceCentre){
                $model->addError('IdServiceCentre', 'Deben seleccionar un Duicentro');
            }
            if(!$model->AppointmentDate){
                $model->addError('AppointmentDate', 'Deben seleccionar una Fecha');
            }
            if($model->errors){
                $message = $this->setMessageErrors($model->errors);
                throw new Exception($message, 91001);
            } else {
                $model->response_format = Appointments::RESPONSE_FORMAT_GRID;
                $list = $model->getAvailableHours();
                $response = [
                    'success'=> TRUE,
                    'list'=> $list,
                ];
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Appointments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appointments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appointments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    private function sendConfirmationMail($model, $action){
        try {
            $subject = '';
            $state = '';
            $url = Url::to(\Yii::$app->params["mainSiteUrl"]["url"]);
            if($action == 'create'){
                $subject = 'Creación';
                $state = 'Registrada';
            } elseif($action == 'update'){
                $subject = 'Reprogramación';
                $state = 'Reprogramada';
            } elseif($action == 'cancel'){
                $subject = 'Cancelación';
                $state = 'Cancelada';
            }
            $body = '<ul> '
                    . '<li>Fecha: <b>'.$model->getAppointmentDate().'</b></li>'
                    . '<li>Hora: <b>'.$model->getAppointmentHour().'</b></li>'
                    . '<li><b>'.$state.'</b></li>'
                    . '<li>Duicentro: <b>'.$model->idServiceCentre->Name.'</b></li>'
                    .(!empty($model->idServiceCentre->Address) ? '<li>Dirección: '.$model->idServiceCentre->Address.'</li>':'')
                    . '<li>Tipo Trámite: <b>'.$model->idType->Name.'</b></li>'
                    . '<li>Código de Confirmación:<br/>'
                    . '<h2>'.$model->ShortCode.'</h2>'
                    . '</li>'
                    . '<li>Código: <strong>'.$model->Code.'</strong>'
                    . '</li>'
                    . '</ul>';
            $footer = "<br/>"
                    . "<b>*Debe presentarse al Duicentro 10 minutos antes de la cita registrada</b><br/>"
                    . "<b>**De no presentarse a la cita a la hora registrada, la cita será cancelada</b><br/>"
                    . "<br/>"
                    . "<b>Visite ".$url." para más información<br/>"
                    ;
            $content = [
                'title'=>'Confirmación de '.$subject.' de Cita',
                'body'=>$body,
                'footer'=>$footer,
            ];
            $email = Yii::$app
                ->mailer
                ->compose(
                    ['html' => '@frontend/mail/default-html'],
                    ['data' => $content]
                )
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($model->idCitizen->Email)
                ->setSubject($content['title'])
                ->send();
            
            if($email){
                #Yii::$app->getSession()->setFlash('success','Revisa la Bandeja de tu Email!');
            } else{
                Yii::$app->getSession()->setFlash('warning','Error al enviar confirmación, contacte al Administrador!');
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function actionValidatedate(){
        $model = new Appointments();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = \Yii::$app->request->post('data');
            $data = Json::decode($data, TRUE);
            $model->attributes = $data;
            if(!$model->IdServiceCentre){
                $model->addError('IdServiceCentre', 'Deben seleccionar un Duicentro');
            }
            if(!$model->AppointmentDate){
                $model->addError('AppointmentDate', 'Deben seleccionar una Fecha');
            }
            $model->dateValidation();
            if($model->errors){
                $message = $this->setMessageErrors($model->errors);
                throw new Exception($message, 91001);
            } else {
                $response = [
                    'success'=> TRUE,
                ];
            }
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
                'errors'=> $model->errors,
            ];
        }
        return $response;
    }
}
