<?php

use common\customassets\fileinput\FileInput;
use kartik\widgets\ActiveForm;
use yii\helpers\StringHelper;
use common\models\Attachment;
use yii\web\JsExpression;


/* @var $model \common\models\Attachment */
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
        <?= $form->field($model, 'fileattachment')->widget(FileInput::class, [
            'id'=> 'fileattachment',
            'options'=> [
                'multiple'=> true,
            ],
            'pluginOptions' => [
                'previewFileType' => 'any',
                'uploadUrl' => $urlDetail."/upload"
                , 'uploadExtraData' => new JsExpression( "() => {".
                        "return {"
                            . "'".StringHelper::basename(Attachment::class)."[KeyWord]':'".$model->KeyWord."',"
                            . "'".StringHelper::basename(Attachment::class)."[AttributeName]':'".$model->AttributeName."',"
                            . "'".StringHelper::basename(Attachment::class)."[AttributeValue]':'".$model->AttributeValue."',"
                            . "'".StringHelper::basename(Attachment::class)."[Description]': $('#".$tableName."-description').val()"
                            . "}"
                        . "}"
                    ),
            ],
            'pluginEvents' => [
                'fileuploaded'=>"function(){ "
                . " $(this).fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh'); "
                . " $('#$modalName').modal('toggle'); "
                . " refreshGridAttachments(); "
                . " $('#$tableName-description').val('');"
                . " }",
            ],
        ])?>
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