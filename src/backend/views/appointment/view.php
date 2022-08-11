<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Appointments */

$this->title = "Cita ".$model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Citas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$url = \Yii::$app->getUrlManager()->createUrl('appointment');
?>
<div class="appointments-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= $model->cancel ?  Html::button('Cancelar Cita',['class' => 'btn btn-danger', 'id'=>'btnCancel']):""; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            [
                'attribute'=>'IdCitizen',
                'value'=>$model->IdCitizen ? $model->citizen->CompleteName:"",
            ],
            'AppointmentDate',
            'AppointmentHour',
            [
                'attribute'=>'IdState',
                'value'=>$model->IdState ? $model->state->Name:"",
            ],
            [
                'attribute'=>'IdServiceCentre',
                'value'=>$model->IdServiceCentre ? $model->serviceCentre->Name:"",
            ],
            'ShortCode',
            'Code',
            'CreationDate',
            'RegistrationMethodName',
        ],
    ]) ?>
    <?= Html::a('Cerrar', ['index'], ['class' => 'btn btn-danger']) ?>

</div>
<?php 
$script = <<< JS
   
$(document).ready(function(){
        
    $("#btnCancel").on('click',function(){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa cancelar la Cita?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, Cancelar!",
            closeOnConfirm: false
        }
        ,function(){
            cancel();
        });
    });
});
  
    var cancel = function(){
        var data = {id: "$model->Id"};
        var params = {};
        params.URL = "$url/cancel";
        params.DATA = {'data':JSON.stringify(data)};
        params.METHOD = "POST";
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            swal({
                title: "Cita Cancelada",
                type: "success",
                text: data.message,
                html: true,
                showCloseButton: true,
            }, function(){
                window.location = "$url/$model->Id";
            });
        };
        params.ERROR = function(data){
            swal({
                title: "Error!",
                type: "error",
                text: data.message,
                html: true,
                showCloseButton: true,
            }, function(){

            });
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($script, \yii\web\VIEW::POS_END);
?>