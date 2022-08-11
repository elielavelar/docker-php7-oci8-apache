<?php

use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model \common\models\Catalogversion */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>
<div class="card-body">
    <div class="row">
        <div class="col-4">
           <?= $form->field($model, 'Version')->textInput(['maxlength' => true, 'id' => $tableName.'-version']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id' => $tableName.'-idstate']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'CurrentVersion')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName.'-currentversion',
                ],
                'pluginOptions' => [
                    'size' => SwitchInput::SIZE_LARGE,
                    'onText' => 'Sí',
                    'offText' => 'No',
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=> 4, 'id' => $tableName.'-description']) ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput(['id' => $tableName.'-id'])->label(FALSE) ?>
    <?= $form->field($model, 'IdCatalog')->hiddenInput(['id' => $tableName.'-idcatalog'])->label(FALSE) ?>
</div>
<?php ActiveForm::end(); ?>