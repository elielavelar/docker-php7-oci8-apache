<?php

use yii\helpers\Html;
use kartik\password\PasswordInput;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use yii\widgets\MaskedInput;
use common\customassets\fileinput\FileInput;
use yii\web\JsExpression;
use common\models\User;
use common\models\Attachment;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelDetail Attachment */
$options = ['autocomplete' => 'off'];
if (!$model->isNewRecord) {
    $options['readonly'] = 'readonly';
}
$parentModel = \yii\helpers\StringHelper::basename(User::class);
$attachModel = \yii\helpers\StringHelper::basename(Attachment::class);
$url = \Yii::$app->getUrlManager()->createUrl('user');
$tableName = 'user';
$formName = $tableName . '-form';

$verdictTitles = [
    0 => 'No Ingresada',
    1 => 'Muy Débil',
    2 => 'Débil',
    3 => 'Aceptable',
    4 => 'Buena',
    5 => 'Excelente'
];
?>
<div class="card-body">
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Username')->textInput($options); ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'CodEmployee')->textInput([]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'FirstName')->textInput(['autocomplete' => 'off']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'SecondName')->textInput(['autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'LastName')->textInput(['autocomplete' => 'off']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'SecondLastName')->textInput(['autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'DisplayName')->textInput(['autocomplete' => 'off']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'DocumentNumber')->widget(MaskedInput::class, [
                'mask' => '99999999-9',
            ]); ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'Birthdate')->widget(DatePicker::class, [
                'id' => $tableName . '-birthdate',
                'size' => DatePicker::SIZE_MEDIUM,
                'language' => 'es',
                'options' => ['placeholder' => 'DD-MM-YYYY'],
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'autoclose' => true,
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Email')->textInput(['autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'IdProfile')->widget(Select2::class, [
                'data' => $model->getProfiles(),
                #'disabled'=> (!$model->isNewRecord),
                'initValueText' => ($model->IdProfile ? $model->profile->Name : ""),
                'options' => ['placeholder' => '--Seleccione Perfil--'],
                'size' => Select2::SIZE_MEDIUM,
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'pluginEvents' => [
                    'change' => "function(){ }",
                ],
            ]);
            ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdServiceCentre')->widget(Select2::class, [
                'data' => $model->getServiceCentres(),
                #'disabled'=> (!$model->isNewRecord),
                'initValueText' => ($model->IdServiceCentre ? $model->serviceCentre->Name : ""),
                'options' => ['placeholder' => '--Seleccione Departamento--'],
                'size' => Select2::SIZE_MEDIUM,
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'pluginEvents' => [
                    'change' => "function(){ }",
                ],
            ]);
            ?>
        </div>

    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []); ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'PasswordExpirationDate')->textInput(['disabled' => TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cambiar Contrase&ntilde;a</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group float-right">
                                <?= Html::label('&nbsp;', 'btn-getrandompass', ['style' => 'display: block', 'class' => 'control-label']) ?>
                                <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> Generar Contrase&ntilde;a', ['class' => 'btn btn-primary control-input', 'id' => 'btn-getrandompass']) ?>
                            </div>
                        </div>
                        <div class="col-4">
                            <?= $form->field($model, '_password')->widget(PasswordInput::class, [
                                'language' => 'es_SV',
                                'pluginOptions' => [
                                    'showMeter' => TRUE,
                                    'verdictTitles' => $verdictTitles,
                                ],
                            ]); ?>
                        </div>
                        <div class="col-4">
                            <?= $form->field($model, '_passwordconfirm')->widget(PasswordInput::class, [
                                'language' => 'es_SV',
                                'pluginOptions' => [
                                    'showMeter' => FALSE,
                                    'toggleMask' => FALSE,
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$valid = <<< JS
        $("#user-firstname, #user-lastname").on('change', function(){
            var _enabled = $('#user-username').not(['readonly']);
            var _fname = jQuery.trim($("#user-firstname").val());
            var _arrayName = _fname.split('');
            var _lname = jQuery.trim($("#user-lastname").val());
            var _username = _lname.toUpperCase() + (_arrayName.length > 0 ? _arrayName[0]:'').toUpperCase();
            var _display =  _fname+ ' ' + _lname;
            $("#user-username").val(_username);
            $("#user-displayname").val(jQuery.trim(_display));
        });
JS;
$validation = $model->isNewRecord ? $valid : '';
$script = <<< JS
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
            $("#$tableName-_password").val(data.password);
            $("#$tableName-_passwordconfirm").val(data.password);
        };
        params.ERROR = function(data){
            swal("ERROR", data.message, "error");
        };
        AjaxRequest(params);
    };
        
    $(document).ready(function(){
        $('#btn-getrandompass').on('click',function(){
            getRandomPass();
        });
        
        $validation
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>