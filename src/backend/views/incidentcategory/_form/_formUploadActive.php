<?php
use yii\widgets\ActiveForm;
use common\customassets\fileinput\FileInput;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

/* @var $model \backend\models\Activetype */
$controller = 'activetype';
$tableName = $model->tableName();
$modalName = $tableName.'-modal';

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
            return {'".StringHelper::basename(get_class($model))."[IdCategoryType]':'".$model->IdCategoryType."'}"
            ."}"),
    ],
    'pluginEvents' => [
        'fileuploaded'=>"function(){ "
            . " $(this).fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh'); "
            . " $('#$modalName').modal('toggle'); "
            . " refreshGrid(); "
            . " }",
        'fileerror' => new JsExpression(" function(data){
            console.log(data);
        }")
    ],
]);

ActiveForm::end();