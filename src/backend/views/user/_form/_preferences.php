<?php
use yii\helpers\Html;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$url = Yii::$app->getUrlManager()->createUrl('user');
$urlDefault = Yii::$app->getUrlManager()->createUrl('user/profile');
?>
<form action="#" method="post">
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Html::a('<i class="fas fa-sync-alt"></i> Recargar Opciones','javascript:void(0);',['id'=> 'btnRefresh','class' => 'btn btn-success']);?>
                </span>
            </div>
        </div>
    </div>
</div>
</form>
    
<?php 
$scriptReady = <<< JS
   $(document).ready(function(){
        $("#btnRefresh").on('click', function(){
            swal({
                title: "Confirmación?",
                text: "¿Está seguro que desesa Eliminar este Registro?",
                type: "success",
                showCancelButton: true,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "Recargar",
                closeOnConfirm: true
            },
            function(){
                reloadPreferences();
            });
        });
   });
JS;
$this->registerJs($scriptReady, yii\web\View::POS_READY);

$scriptHead= <<< JS
    var reloadPreferences = function(){
        var params = {};
        params.URL = "$url/reloadoptions";
        params.DATA = {},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.CACHE = false;
        params.SUCCESS = function(data){
            swal({
                title: "Opciones Recargadas",
                text: data.message,
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "Aceptar",
                closeOnConfirm: true
            },
            function(){
                window.location = '$urlDefault';
            });
            
        };
        params.ERROR = function(data){
            swal("ERROR", data.message, "error");
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($scriptHead, yii\web\View::POS_HEAD);
?>