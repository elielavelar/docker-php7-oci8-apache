<?php
use kartik\widgets\ActiveForm;
use kartik\password\PasswordInput;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */
$verdictTitles = [
    0 => 'No Ingresada',
    1 => 'Muy Débil',
    2 => 'Débil',
    3 => 'Aceptable', 
    4 => 'Buena',
    5 => 'Excelente'
];
$prefixName = 'datosoperchange';
$tableName = 'DatosOper';
$url = Yii::$app->getUrlManager()->createUrl('sdmsoperator');
$urlView = Yii::$app->getUrlManager()->createUrl(['sdmsoperator/view','id'=>$model->COD_OPER]);
$formName = 'changepassword-form';
$modalName = "modal-changepass";
?>
<?php Modal::begin([
        'options' => [
            'id' => $modalName,
        ],
        'title' => '<h3>Actualizar Contraseña Operador: '.$model->COD_OPER.'</h3>',
        'headerOptions' => ['class' => 'bg-primary'],
        'toggleButton' => ['label' => '<i class="fas fa-sync"></i> Cambiar Contraseña', 'class'=> 'btn btn-warning'],
        'footer' => Html::button('<i class="fas fa-sync"></i> Actualizar',['class'=> 'btn btn-success', 'id'=> 'btnUpdate']).""
                . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class'=> 'btn btn-danger','id'=> 'btnCancelUpd'])
    ])?>
    <?php $form = ActiveForm::begin([
        'id'=> $formName,
    ]); ?>
    <div class='card-body'>
        <div class='row'>
            <div class="col-3">
                <div class="form-group">
                    <label class="control-label" for="btn-getRandomPass" style="display: block">&nbsp;</label>
                    <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> Generar Contrase&ntilde;a', ['class'=>'btn control-input','id'=>'btn-getRandomPass'])?>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class="col-12">
                <?=$form->field($model, '_password')->widget(PasswordInput::className(), [
                    'language'=>'es_SV',
                    'pluginOptions'=>[
                        'showMeter'=>TRUE,
                        'verdictTitles' => $verdictTitles,
                    ],
                    'options' => [
                        'id' => $prefixName.'-_password',
                    ],
                ]);?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?=$form->field($model, '_passwordconfirm')->widget(PasswordInput::className(), [
                    'language'=>'es_SV',
                    'pluginOptions'=>[
                        'showMeter'=>FALSE,
                        'toggleMask'=>FALSE,
                    ],
                    'options' => [
                        'id' => $prefixName.'-_passwordconfirm',
                    ],
                ]);?>
            </div>
        </div>
    </div>
    <?=$form->field($model, 'COD_OPER')->hiddenInput([
        'id'=> $prefixName."-cod_oper",
    ])->label(FALSE);?>
    <?php ActiveForm::end(); ?>
<?php Modal::end();?>
<?php
$js = <<< JS
        
   var getRandomPass = function(){
        var params = {};
        params.URL = "$url/getrandompass";
        params.DATA = {},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.CACHE = false;
        params.SUCCESS = function(data){
            $("#$prefixName-_password").val(data.password);
            $("#$prefixName-_passwordconfirm").val(data.password);
        };
        params.ERROR = function(data){
            swal("ERROR", data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$prefixName-_password':'$model->_password'
            , '$prefixName-_passwordconfirm':'$model->_passwordconfirm'
            , '$prefixName-cod_oper':'$model->COD_OPER'});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };

   $(document).ready(function(){
        $("#btnCancelUpd").on('click', function(){
            $("#$modalName").modal("toggle");
        });
        
        $('#btn-getRandomPass').on('click',function(){
            getRandomPass();
        });
        
        $("#btnUpdate").on('click', function(){
            $("#$formName").data('yiiActiveForm').submitting = true;
            $("#$formName").yiiActiveForm('validate');
        });
        
        $('#$formName').on('beforeSubmit', function (e) {
            $.ajax({
                url: "$url/updatepassword",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {		
                    //var data = JSON.parse(data);
                    if(data.success == true)
                    {
                        swal({
                            title: data.title,
                            text: data.message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#00A65A",
                            confirmButtonText: "Aceptar",
                            closeOnConfirm: true
                        }, function(){
                            $("#$modalName").modal("toggle");
                            window.location = '$urlView';
                        });
                    }
                    else
                    {
                        swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                        if(data.errors){
                            var errors = {};
                            errors.ID = "$formName";
                            errors.PREFIX = "$prefixName-";
                            errors.ERRORS = data.errors;
                            setErrorsModel(errors);
                        }
                    }
                },
                error: function() 
                {
                    console.log(data);
                } 	        
           });
        }).on('submit', function(e){
            e.preventDefault();
        });
   });
JS;
$this->registerJs($js);
?>