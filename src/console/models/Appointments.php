<?php

namespace console\models;
use common\models\State;
use common\models\Type;
use common\models\Servicecentres;
use console\models\Citizen;

use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use Yii;

/**
 * This is the model class for table "appointments".
 *
 * @property integer $Id
 * @property integer $IdCitizen
 * @property string $AppointmentDate
 * @property string $AppointmentHour
 * @property string $Code
 * @property integer $IdState
 * @property integer $IdServiceCentre
 * @property integer $IdType
 * @property string $CreationDate
 */
class Appointments extends \yii\db\ActiveRecord
{
    
    const ACTIVE_STATUS = 'ACT';
    const CANCELED_STATUS = 'CAN';
    private $date_format = 'd-m-Y';
    private $dbDateFormat = '%d-%m-%Y';
    private $_date;
    private $dayname = [
        '1'=>'Lunes',
        '2'=>'Martes',
        '3'=>'Miércoes',
        '4'=>'Jueves',
        '5'=>'Viernes',
        '6'=>'Sábado',
        '7'=>'Domingo',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdCitizen', 'AppointmentDate', 'AppointmentHour', 'Code', 'IdState', 'IdServiceCentre', 'IdType'], 'required'],
            [['IdCitizen', 'IdState', 'IdServiceCentre', 'IdType'], 'integer'],
            [['AppointmentDate', 'AppointmentHour', 'CreationDate'], 'safe'],
            [['Code'], 'string', 'max' => 50],
        ];
    }
    
    public function getDateFormat(){
        return $this->date_format;
    }
    
    public function getDbDateFormat(){
        return $this->dbDateFormat;
    }
    
    public function setDateParam($date = NULL){
        $this->_date = $date;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdCitizen' => 'Id Citizen',
            'AppointmentDate' => 'Appointment Date',
            'AppointmentHour' => 'Appointment Hour',
            'Code' => 'Code',
            'IdState' => 'Id State',
            'IdServiceCentre' => 'Id Service Centre',
            'IdType' => 'Id Type',
            'CreationDate' => 'Creation Date',
        ];
    }
    
     public function getAppointmentDate(){
        try {
            $dia = $this->dayname[\Yii::$app->formatter->asDate($this->AppointmentDate, 'e')];
            return $dia." ".\Yii::$app->formatter->asDate($this->AppointmentDate, 'php:d/m/Y');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function getAppointmentHour(){
        try {
            return \Yii::$app->formatter->asTime($this->AppointmentHour, 'php:h:i a');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function cancelPastDate($criteria = []){
        try {
            $status = State::findOne(['KeyWord'=> StringHelper::basename(self::className()),'Code'=> self::CANCELED_STATUS])->Id;
            $statusAct = State::findOne(['KeyWord'=> StringHelper::basename(self::className()),'Code'=> self::ACTIVE_STATUS])->Id;
            if($this->_date == NULL){
                $date =  date_create(date($this->date_format));
                $this->_date = date_format($date, $this->getDateFormat());
            }
            $condition = [
                'and', 
                ["<="," date_format(AppointmentDate, '$this->dbDateFormat')",$this->_date],
                ['IdState'=> $statusAct]
            ];
            
            $records = Appointments::find()->where($condition)->asArray()->all();
            $record = Appointments::updateAll(['IdState'=> $status],$condition);
            return $records;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function sendMailConfirmationBatch($condition = [], $action = 'create'){
        try {
            $url = Url::to(\Yii::$app->params["mainSiteUrl"]["url"]);
            $mails = [
                'SentMail'=>[],
                'FailedMail'=>[],
            ];
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
            $status = State::findOne(['KeyWord'=> StringHelper::basename(self::className()),'Code'=> self::ACTIVE_STATUS])->Id;
            if($this->_date != NULL){
                $models = self::find()->where($condition)
                    ->andWhere("IdState = :idState",[':idState'=>$status])
                    ->andWhere("date_format(AppointmentDate,'$this->dbDateFormat') = :fecha",[':fecha'=> $this->_date])
                    ->all()
                    ;
            } else {
                $models = self::find()->where($condition)
                    ->andWhere("IdState = :idState",[':idState'=>$status])
                    ->all()
                    ;
            }

            if($models != NULL){
                foreach ($models as $m){
                    $model = Appointments::findOne($m["Id"]);
                    $service = Servicecentres::findOne(['Id'=>$model->IdServiceCentre]);
                    $type = Type::findOne(['Id'=>$model->IdType]);
                    $citizen = Citizen::findOne(['Id'=>$model->IdCitizen]);
                    if(!empty($citizen->Email)){
                        $body = '<ul> '
                                . '<li>Fecha: <b>'.$model->getAppointmentDate().'</b></li>'
                                . '<li>Hora: <b>'.$model->getAppointmentHour().'</b></li>'
                                . '<li><b>'.$state.'</b></li>'
                                . '<li>Duicentro: <b>'.$service->Name.'</b></li>'
                                . '<li>Tipo Trámite: <b>'.$type->Name.'</b></li>'
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
                            'title'=>$subject.' de Cita Programada',
                            'body'=>$body,
                            'footer'=>$footer,
                        ];
                        $email = Yii::$app
                            ->mailer
                            ->compose(
                                ['html' => '@console/mail/default-html'],
                                ['data' => $content]
                            )
                            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                            ->setTo($citizen->Email)
                            ->setSubject($content['title'])
                            ->send();
                        if($email){
                            array_push($mails["SentMail"],$citizen->Email);
                        } else {
                            array_push($mails["FailedMail"],$citizen->Email);
                        }
                    }
                    
                }
            }
            $text_response = "Envíos Exitosos: ".implode("\n", $mails["SentMail"]);
            $text_response .= "\nEnvíos Exitosos: ".implode("\n", $mails["FailedMail"]);
            
            $this->composeAdminMail($mails);
            
            return $text_response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function composeAdminMail($mails = ['SentMail'=>[],'FailedMail'=>[]]){
        try {
            $html_response = "<b>Envíos Exitosos: </b>"
                    . "<br/><ul>";
            $i = 1;
            $j = 1;
            foreach ($mails["SentMail"] as $mail) {
                $html_response .= "<li>".$i."- ".$mail."</li>";
                $i++;
            }
            $html_response .= "</ul>";
            $html_response .= "<b>Envíos Fallidos: </b>"
                    . "<br/><ul>";
            foreach ($mails["FailedMail"] as $mail) {
                $html_response .= "<li>".$j."- ".$mail."</li>";
                $j++;
            }
            $html_response .= "</ul>";
            $success = count($mails["SentMail"]);
            $failed = count($mails["FailedMail"]);
            $footer = "<b>Total: </b>".($success + $failed)
                    ."<br/>"
                    . "<ul>"
                    . "<li>Exitosos: <b>".$success."</b></li>"
                    . "<li>Fallidos: <b>".$failed."</b></li>"
                    . "</ul>";
            
            
            $content_admin = [
                'title' => 'Registro de envío de Correo Automático',
                'body'=> $html_response,
                'footer' => $footer,
            ];
            
            $this->sendAdminMail($content_admin);
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    private function sendAdminMail($content_admin = []){
        try {
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
            throw $ex;
        }
        
    }
}
