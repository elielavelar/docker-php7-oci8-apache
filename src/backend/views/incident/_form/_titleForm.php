<?php
use kartik\form\ActiveForm;
/* @var $model \backend\models\Incidenttitle */
$tableName = $model->tableName();
$formName = $tableName.'-form';

$form = ActiveForm::begin([
    'id' => $formName,
]);
?>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'Title')->textarea(['rows' => 5])?>
    </div>
</div>
<?= $form->field($model, 'Id')->hiddenInput()->label(false) ?>
<?php
    ActiveForm::end();
?>
