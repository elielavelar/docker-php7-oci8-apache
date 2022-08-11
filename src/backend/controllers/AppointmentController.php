<?php

namespace backend\controllers;

use Yii;
use common\models\Appointment;
use common\models\AppointmentSearch;
use common\models\State;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\Response;
use Exception;

/**
 * AppointmentController implements the CRUD actions for Appointments model.
 */
class AppointmentController extends CustomController
{
    
    public $customactions = [
        'gethours','get','save','validatedate'
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
        $searchModel = new AppointmentSearch();
        $availabilityModel = new Appointment();
        $availabilityModel->AppointmentDate = date('d-m-Y');
        $queryParams = array_merge([],  Yii::$app->request->getQueryParams());
        $user = \Yii::$app->user->getIdentity();
        $iddept = ($user->IdServiceCentre ? ($user->serviceCentre->type->Code == 'DUISITE' ? $user->IdServiceCentre:NULL):NULL);
        if(!isset($queryParams['AppointmentSearch'])){
            $IdState = State::findOne(['KeyWord'=> StringHelper::basename(Appointment::class),'Code'=>'ACT'])->Id;
            $queryParams['AppointmentSearch']['IdState'] = $IdState;
            $queryParams['AppointmentSearch']['AppointmentDate'] = date('d-m-Y');
            #$queryParams['AppointmentSearch']['hourDate'] = date('H').":00";
            if($iddept){
                $queryParams['AppointmentSearch']['IdServiceCentre']= $iddept;
            }
            
        } elseif(!\Yii::$app->user->can('appointmentFilterStatus')){
            $IdState = State::findOne(['KeyWord'=> StringHelper::basename(Appointment::class),'Code'=>'ACT'])->Id;
            $queryParams['AppointmentSearch']['IdState'] = $IdState;
        }
        
        if(!\Yii::$app->user->can('appointmentFilterByServiceCentre') && !isset($queryParams['AppointmentSearch']['IdServiceCentre'])){
            if($iddept){
                $queryParams['AppointmentSearch']['IdServiceCentre']= $iddept;
            }
        }
        $dataProvider = $searchModel->search($queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'availabilityModel' => $availabilityModel,
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
        $model = new Appointment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionSave(){
        $model = new Appointment();
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post(StringHelper::basename(Appointment::class));
//                $data = json_decode($data, TRUE);
                $action = 'create';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $action = 'update';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;

                if($model->save()){
                    $model->refresh();
                    if($model->citizen->Email != NULL){
                        $action = $model->state->Code == 'CAN' ? 'cancel':$action;
                        $this->sendConfirmationMail($model, $action);
                    }
                    switch ($action){
                        case 'create':
                            $subject = 'Creación ';
                            $state = 'Registrada';
                            break;
                        case 'update':
                            $subject = 'Reprogramación';
                            $state = 'Reprogramada';
                            break;
                        case 'cancel':
                            $subject = 'Cancelación';
                            $state = 'Cancelada';
                            break;
                        case 'reminder':
                            $subject = 'Recordatorio ';
                            $state = 'Registrada';
                            break;
                        default :
                            $subject = 'Creación ';
                            $state = 'Registrada';
                    }
                    $message = 'Cita '.$state." Exitosamente";
                    $response = array_merge(['success'=>true,'message'=>$message, 'subject' => $subject],$model->attributes);
                } else {
                    $message = $this->setMessageErrors($model->errors);
                    throw new Exception($message, 90002);
                }
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
        //echo json_encode($response);
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
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionCancel(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Appointment();
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $values = Json::decode($data);
                $model = Appointment::findOne($values);
                if($model == NULL ){
                    throw new Exception('Cita no encontrada', 90001);
                }
                $model->scenario = Appointment::SCENARIO_CANCEL;
                if($model->cancel()){
                    $this->sendConfirmationMail($model, $this->action->id);
                    $response = [
                        'success'=>TRUE,
                        'message'=>'Cita Cancelada Exitosamente',
                    ];
                } else {
                    if(!empty($model->errors)){
                        foreach ($model->errors as $error){
                            $message = (implode("- ", $error));
                            throw new Exception($message, 90002);
                        }
                    } else {
                        throw new Exception('ERROR DESCONOCIDO', 90003);
                    }
                }
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>FALSE,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }

    /**
     * Deletes an existing Appointments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        if (($model = Appointment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGet(){
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
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
        return $response;
    }
    
    public function actionSendremindermail($id){
        try {
            if($id == NULL){
                throw new Exception("Cita no seleccionada", 91000);
            }
            $model = new Appointment();
            $model->sendMailConfirmationBatch(['Id'=>$id],'reminder');
            \Yii::$app->session->setFlash('success', 'Correo enviado satisfactoriamente');
        } catch (Exception $ex) {
            \Yii::$app->session->setFlash('error', $ex->getTraceAsString());
        }
        return $this->redirect(['index']);
    }
    
    private function sendConfirmationMail($model, $action){
        try {
            $subject = '';
            $state = '';
            $url = Url::to(\Yii::$app->params["mainSiteUrl"]["url"]);
            switch ($action){
                case 'create':
                    $subject = 'Creación ';
                    $state = 'Registrada';
                    break;
                case 'update':
                    $subject = 'Reprogramación';
                    $state = 'Reprogramada';
                    break;
                case 'cancel':
                    $subject = 'Cancelación';
                    $state = 'Cancelada';
                    break;
                case 'reminder':
                    $subject = 'Recordatorio ';
                    $state = 'Registrada';
                    break;
                default :
                    $subject = 'Creación ';
                    $state = 'Registrada';
            }
            
            $body = '<ul> '
                    . '<li>Fecha: <b>'.$model->getAppointmentDate().'</b></li>'
                    . '<li>Hora: <b>'.$model->getAppointmentHour().'</b></li>'
                    . '<li><b>'.$state.'</b></li>'
                    . '<li>Duicentro: <b>'.$model->serviceCentre->Name.'</b></li>'
                    . '<li>Tipo Trámite: <b>'.$model->type->Name.'</b></li>'
                    . '<li>Código de Confirmación:<br/>'
                    . '<h2>'.$model->ShortCode.'</h2>'
                    . '</li>'
                    . '<li>Código: <strong>'.$model->Code.'</strong>'
                    . '</li>'
                    . '</ul>';
            $footer = "<br/>"
                        . "<b>*Debe presentarse al Duicentro 10 minutos antes de la cita registrada</b><br/>"
                        . "<b>**De no presentarse a la cita a la hora registrada, la cita será cancelada</b><br/>"
                        . "<span style='color:red; font-weight: bolder'><h3>*** Debe presentarse al Duicentro seleccionado</h3></span><br/>"
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
                    ['html' => '@backend/mail/default-html'],
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
    
    public function actionGethours(){
        $response = [];
        $model = new Appointment();
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
                $model->response_format = Appointment::RESPONSE_FORMAT_GRID;
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
    
    public function actionValidatedate(){
        $model = new Appointment();
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
