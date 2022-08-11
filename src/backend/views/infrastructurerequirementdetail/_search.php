<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\InfrastructurerequirementdetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="infrastructurerequirementdetails-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdInfrastructureRequirement') ?>

    <?= $form->field($model, 'Title') ?>

    <?= $form->field($model, 'Description') ?>

    <?= $form->field($model, 'DetailDate') ?>

    <?php // echo $form->field($model, 'RecordDate') ?>

    <?php // echo $form->field($model, 'SolutionDate') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <?php // echo $form->field($model, 'IdActivityType') ?>

    <?php // echo $form->field($model, 'IdRequirementState') ?>

    <?php // echo $form->field($model, 'IdAssignedUser') ?>

    <?php // echo $form->field($model, 'Commentaries') ?>

    <?php // echo $form->field($model, 'IdCatalogDetailValue') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
