<?php

use kartik\widgets\ActiveForm;
use yii\web\JsExpression;


/* @var $model \backend\models\Incidentresource */
/* @var $form yii\widgets\ActiveForm */

$urlDetail = \Yii::$app->getUrlManager()->createUrl('attachment');
$tableName = $model->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';

?>
<?php
$form = ActiveForm::begin([
    'id' => $formName,
    'options'=>['enctype'=>'multipart/form-data']
]);
?>
    <div class="row">
        <div class="col-12">

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model, 'Description')->textarea(['rows'=>4, 'id'=>$tableName."-description"])?>
        </div>
    </div>
<?php
ActiveForm::end();
?>