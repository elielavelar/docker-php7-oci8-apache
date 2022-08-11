<?php
use yii\bootstrap4\Modal;
use yii\widgets\ActiveForm;
use common\customassets\fileinput\FileInput;
use yii\helpers\Url;

/* @var $model \common\models\User */

Modal::begin([
    'title' => '<h4>Cargar Listado desde Archivo</h4>',
    'headerOptions' => [
        'class' => 'bg-blue',
    ],
    'toggleButton' => [
        'label' => '<i class="fa fa-upload"></i> Crear desde Archivo', 'class' => 'btn btn-primary',
    ],
]);
$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]);
echo $form->field($model, 'uploadFile')->widget(FileInput::class, [
    'pluginOptions' => [
        'uploadUrl' => Url::to(['user/uploadbatch']),
        'showPreview' => false,
    ],
]);

ActiveForm::end();
Modal::end();
