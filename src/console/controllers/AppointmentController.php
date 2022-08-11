<?php

namespace console\controllers;

use console\models\Appointments;
use backend\models\Settingsdetail;
use yii\console\Controller;
use yii\console\Response;
use common\models\LoginForm;
use Exception;
use Yii;

class AppointmentController extends Controller
{
    
    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        //$authKey = \Yii::$app->params["authKey"];
        //$this->logon($authKey);
        
    }
    public function actionCancel($day = NULL){
        try{
            $day = $day ? $day:0;
            $model = new Appointments();
            $date = date_create(date($model->getDateFormat()));
            if($day > 0){
                date_sub($date, date_interval_create_from_date_string("$day day"));
            }
            $d = date_format($date, $model->getDateFormat());
            $model->setDateParam($d);
            $response = $model->cancelPastDate([]);
            $count = count($response);
            $detail = "<ul>";
            foreach ($response as $det){
                $detail .= "<li>Id: ".$det["Id"].", ShortCode: ".$det["ShortCode"]."</li>";
            }
            $detail .= "</ul>";
            $body = "Cantidad de Registros Cancelados: ".$count
                    . "<br>"
                    . $detail;
            
            $content_admin = [
                'title'=>'Confirmación Cancelación Automática de Citas '.$date->format($model->getDateFormat()),
                'body'=> $body,
                'footer'=>''
            ];
            $this->sendAdminMail($content_admin);
            print_r($response);
            #echo $response;
        } catch (Exception $exc){
#            echo $exc->getTraceAsString();
            print_r($exc);
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSendreminder($day = NULL)
    {
        try {
            if($day == NULL){
                $value = Settingsdetail::find()->where(['settingsdetail.Code'=>'RMND'])
                        ->joinWith('idSetting b',true)
                        ->andWhere(['b.KeyWord'=>'Appointment','b.Code'=>'RMND'])
                        ->one();
                if($value != NULL){
                    $day = $value->Value;
                } else {
                    $day = 1;
                }
            }
            
            $model = new Appointments();
            $date = date_create(date($model->getDateFormat()));
            date_add($date, date_interval_create_from_date_string("$day day"));
            $d = date_format($date, $model->getDateFormat());
            $model->setDateParam($d);
            $response = $model->sendMailConfirmationBatch([], 'reminder');
            echo $response;
        } catch (Exception $exc) {
            print_r($exc);
        }
    }
    
    private function logon($authkey){
        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_WEBSERVICE;
        $model->authkey = $authkey;
        return $model->loginByKey();
    }

    private function sendAdminMail($content_admin = []){
        try {
            if(empty($content_admin)){
                $content_admin = [
                    'title'=> 'Correo confirmación Sistema',
                    'body'=>'',
                    'footer'=>'',
                ];
            }
            $email = Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => '@console/mail/default-html'],
                            ['data' => $content_admin]
                        )
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                        ->setTo(\Yii::$app->params['supportEmail'])
                        ->setSubject($content_admin['title'])
                        ->send();
        } catch (Exception $ex) {
            echo $ex->getTraceAsString();
        }
        
    }
    
}
