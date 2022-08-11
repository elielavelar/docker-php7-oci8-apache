<?php

namespace backend\controllers;

use Yii;
use common\models\AppointmentsSearch;
use common\models\Appointments;
use common\models\State;
use Exception;

class MaintenanceController extends \yii\web\Controller
{
   /**
     * Lists all Appointments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $options = [
            [
                'name'=>'Enviar Correo Confirmación',
                'url'=>'maintenance/sendmail',
                'icon'=>'fa fa-envelope-o',
            ],
        ];
        
        return $this->render('index', [
            'options' => $options,
        ]);
    }
    
    public function actionSendmail(){
        $searchModel = new AppointmentsSearch();
        $queryParams = array_merge([],  Yii::$app->request->getQueryParams());
        if(!isset($queryParams['AppointmentsSearch'])){
            $IdState = State::findOne(['KeyWord'=>'Appointments','Code'=>'ACT'])->Id;
            $queryParams['AppointmentsSearch']['IdState'] = $IdState;
            $queryParams['AppointmentsSearch']['AppointmentDate'] = date('d-m-Y');
            
        } else {
            $IdState = State::findOne(['KeyWord'=>'Appointments','Code'=>'ACT'])->Id;
            $queryParams['AppointmentsSearch']['IdState'] = $IdState;
        }
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('sendmail', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionSendbatchmail(){
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = json_decode($data, TRUE);
                
                $models = new Appointments();
                if(isset($data["AppointmentDate"])){
                    $models->setDateParam($data["AppointmentDate"]);
                    unset($data["AppointmentDate"]);
                }
                
                $models->sendMailConfirmationBatch($data, 'reminder');

                $response = [
                    'success'=>TRUE,
                    'message'=>'Correos enviados satisfactoriamente',
                ];
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$models->errors,
            ];
        }
        echo json_encode($response);
    }
    
    public function actionSendremindermail($id){
        try {
            if($id == NULL){
                throw new Exception("Cita no seleccionada", 91000);
            }
            $model = new Appointments();
            $model->sendMailConfirmationBatch(['Id'=>$id],'reminder');
            \Yii::$app->session->setFlash('success', 'Correo enviado satisfactoriamente');
        } catch (Exception $ex) {
            \Yii::$app->session->setFlash('error', $ex->getTraceAsString());
        }
        return $this->redirect(['sendmail']);
    }

}
