<?php

use kartik\widgets\FileInput;
use kartik\widgets\ActiveForm;
use yii\helpers\StringHelper;
use backend\models\Attachments;
use yii\web\JsExpression;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* @var $model backend\models\Attachments */
/* @var $form yii\widgets\ActiveForm */

$urlDetail = \Yii::$app->getUrlManager()->createUrl('attachment');
$tableName = $model->tableName();
$formName = $tableName.'-form';

?>
<?php
$form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data']
]);
?>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'fileattachment')->widget(FileInput::class, [
            'id'=> 'fileattachment',
            'options'=> [
                'multiple'=> TRUE,
            ],
            'pluginOptions' => [
                'previewFileType' => 'any',
                'uploadUrl' => $urlDetail."/upload"
                , 'uploadExtraData' => new JsExpression("function(){"
                        . "return {"
                            . "'".StringHelper::basename(Attachments::class)."[KeyWord]':'".$model->KeyWord."',"
                            . "'".StringHelper::basename(Attachments::class)."[AttributeName]':'".$model->AttributeName."',"
                            . "'".StringHelper::basename(Attachments::class)."[AttributeValue]':'".$model->AttributeValue."',"
                            . "'".StringHelper::basename(Attachments::class)."[Description]': $('#".$model->tableName()."-description').val()"
                            . "}"
                        . "}"),
                #'showPreview' => false,
            ],
            'pluginEvents' => [
                'fileuploaded'=>"function(){ "
                . " $(this).fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh'); "
                . " $('#modal-attachment').modal('toggle'); "
                . " refreshGridAttachments(); "
                . " $('#$tableName-description').val('');"
                . " }",
            ],
        ])?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?=$form->field($model, 'Description')->textarea(['rows'=>4, 'id'=>$tableName."-description"])?>
    </div>
</div>
<?php
ActiveForm::end();
?>