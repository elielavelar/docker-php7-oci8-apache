<?php
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

/* @var $model \backend\models\Problemtype */
$controller = 'problemtype';
$tableName = $model->tableName();
$modalName = $tableName.'-upload-modal';

$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]);
echo $form->field($model, 'uploadFile')->widget(FileInput::class, [
    'pluginOptions' => [
        'uploadUrl' => Url::to([$controller.'/uploadbatch']),
        'showPreview' => false,
        'uploadExtraData' => new JsExpression( "() => {
            return {'".StringHelper::basename(get_class($model))."[IdActiveType]':'".$model->IdActiveType."'}"
            ."}"),
    ],
    'pluginEvents' => [
        'fileuploaded'=>" function( event, data, previewId, index, fileId){ "
            . " $(this).fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh'); "
            . " $('#$modalName').modal('toggle'); "
            . " refreshGrid(); "
            . " }",
        'fileuploaderror' => new JsExpression(" function(event, data, message){
            swal({
                title: 'Error',
                text: data.jqXHR.responseJSON.error,
                dangerMode: true,
                icon: 'warning'
            });
        }"),
    ],
]);

ActiveForm::end();