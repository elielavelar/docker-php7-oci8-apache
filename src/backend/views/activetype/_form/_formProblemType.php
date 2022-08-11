<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Problemtype */
/* @var $form yii\widgets\ActiveForm */
$controller = 'problemtype';
$tableName = $model->tableName();
$formName = $tableName.'-form';

$form = ActiveForm::begin([
    'id' => $formName,
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]);
?>

<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-3">
        <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-6">
        <?= $form->field($model, 'IdComponentType')->dropDownList($model->getComponentTypes(),['prompt'=>'--']) ?>
    </div>
    <div class="col-3">
        <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]) ?>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=> 4]) ?>
    </div>
</div>
<?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
<?= $form->field($model, 'IdActiveType')->hiddenInput()->label(FALSE) ?>

<?php ActiveForm::end(); ?>