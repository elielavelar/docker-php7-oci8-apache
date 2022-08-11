<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirementtype */
/* @var $form yii\widgets\ActiveForm */

$prefix = $tableName;
?>
<?php $form = ActiveForm::begin([
    'id' => $formName
]); ?>
<div class="card-body">
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true,]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-6'>
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true,]) ?>
        </div>
        <div class='col-6'>
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'IdServiceCentre')->dropDownList($model->getServiceCentres(), ['prompt' => '--SELECCIONE DEPARTAMENTO--']);
            ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-12'>
    <?= $form->field($model, 'Description')->textarea(['rows' => 6,]) ?>
        </div>
    </div>
<?= $form->field($model, 'Id')->hiddenInput([])->label(false); ?>
<?= $form->field($model, 'IdParent')->hiddenInput([])->label(false); ?>
</div>
<?php ActiveForm::end(); ?>
