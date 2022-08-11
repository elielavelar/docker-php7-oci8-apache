<?php

use kartik\widgets\FileInput;
use kartik\widgets\ActiveForm;
use yii\helpers\StringHelper;
use common\models\Attachment;
use yii\web\JsExpression;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* @var $model \common\models\Attachment */
/* @var $formAttachment yii\widgets\ActiveForm */

$urlDetail = \Yii::$app->getUrlManager()->createUrl('attachment');
$tableName = $model->tableName();
$formName = $tableName.'-form';

?>
<?php
$formAttachment = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data']
]);
?>
<div class="row">
    <div class="col-12">
        <?= $formAttachment->field($model, 'fileattachment')->widget(FileInput::class, [
            'id'=> 'fileattachment',
            'options'=> [
                'multiple'=> TRUE,
            ],
            'pluginOptions' => [
                'previewFileType' => 'any',
                'uploadUrl' => $urlDetail."/upload"
                , 'uploadExtraData' => new JsExpression("function(){"
                        . "return {"
                            . "'".StringHelper::basename(Attachment::class)."[KeyWord]':'".$model->KeyWord."',"
                            . "'".StringHelper::basename(Attachment::class)."[AttributeName]':'".$model->AttributeName."',"
                            . "'".StringHelper::basename(Attachment::class)."[AttributeValue]':'".$model->AttributeValue."',"
                            . "'".StringHelper::basename(Attachment::class)."[Description]': $('#".$model->tableName()."-description').val(),"
                            . "'".StringHelper::basename(Attachment::class)."[overwrite]': ".Attachment::OVERWRITE_ENABLED
                            . "}"
                        . "}"),
                #'showPreview' => false,
            ],
            'pluginEvents' => [
                'fileuploaded'=>"function(){ "
                . " $(this).fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh'); "
                . " $('#modal-attachment').modal('toggle'); "
                . " refreshWindow(); "
                . " $('#$tableName-description').val('');"
                . " }",
            ],
        ])?>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?=$formAttachment->field($model, 'Description')->textarea(['rows'=>4, 'id'=>$tableName."-description"])?>
    </div>
</div>
<?php
ActiveForm::end();
?>