<?php
use yii\bootstrap4\ActiveForm;
use common\customassets\fileinput\FileInput;
use yii\helpers\Url;
/* @var $model \backend\models\Incidenttitle */
$controller = Yii::$app->controller->id;


$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]);
echo $form->field($model, 'uploadFile')->widget(FileInput::class, [
    'pluginOptions' => [
        'uploadUrl' => Url::to($controller.'/upload'),
        'showPreview' => false,
    ],
]);

ActiveForm::end();