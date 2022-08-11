<?php

use yii\helpers\Html;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */

$this->title = 'Agregar Operador';
$this->params['breadcrumbs'][] = ['label' => 'Operadores SDMS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$tableName = 'datosoper';
$formName = $tableName."-form";
$url = Yii::$app->getUrlManager()->createUrl('sdmsoperator');

?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
<?php
$js =  <<< JS
   
   $(document).ready(function(){
        $("#$tableName-cod_empleado, #$tableName-nom1_oper, #$tableName-apdo1_oper, #$tableName-cod_rol, #$tableName-cod_cargo_oper, #$tableName-cod_ctro_serv").on('change', function(){
            var form = {};
            form.ID = '$formName';
            form.PREFIX = '$tableName-';
            var data = getValuesForm(form);
            var success = function(data){
                $("#$tableName-cod_oper").val(data.COD_OPER);
            };
            var error = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formName";
                    errors.PREFIX = "$tableName-";
                    errors.ERRORS = data.errors;
                    setErrorsModel(errors);
                }
            };
            var params = {};
            params.URL = "$url/getcode";
            params.DATA = {'data':JSON.stringify(data)},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = success;
            params.ERROR = error;
        
            AjaxRequest(params);
        });
   });
JS;
$this->registerJs($js, View::POS_READY);
?>