<?php
use yii\bootstrap4\ActiveForm;

/* @var $model \common\models\Personaldocument */
$tableName = $model->tableName();
$formName = $tableName.'-form';

$form = ActiveForm::begin([
    'id' => $formName,
]);
?>
<div class="row">
    <div class="col-12">
        <?=$form->field($model, 'IdDocumentType')->dropDownList($model->getDocumentTypes(), ['id' => $tableName.'-iddocumenttype']);?>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?=$form->field($model, 'DocumentNumber')->textInput(['id' => $tableName.'-documentnumber']);?>
    </div>
</div>
<?=$form->field($model, 'Id')->hiddenInput(['id' => $tableName.'-id'])->label(false);?>
<?=$form->field($model, 'IdPerson')->hiddenInput(['id' => $tableName.'-idperson'])->label(false);?>
<?php ActiveForm::end();?>