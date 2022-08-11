<?php
use yii\widgets\ActiveForm;
use common\customassets\fileinput\FileInput;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

/* @var $model \backend\models\Incidentcategory */
$controller = Yii::$app->controller->id;

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
            return {'".StringHelper::basename(get_class($model))."[IdParent]':'".$model->Id."'}"
        ."}"),
    ],
]);

ActiveForm::end();