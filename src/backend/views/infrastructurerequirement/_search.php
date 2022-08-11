<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\InfrastructurerequirementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="infrastructurerequirement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Ticket') ?>

    <?= $form->field($model, 'TicketDate') ?>

    <?= $form->field($model, 'RequirementDate') ?>

    <?= $form->field($model, 'SolutionDate') ?>

    <?php // echo $form->field($model, 'Title') ?>

    <?php // echo $form->field($model, 'IdServiceCentre') ?>

    <?php // echo $form->field($model, 'IdIncident') ?>

    <?php // echo $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'IdInfrastructureRequirementType') ?>

    <?php // echo $form->field($model, 'IdReportUser') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <?php // echo $form->field($model, 'AffectsFunctionality') ?>

    <?php // echo $form->field($model, 'AffectsSecurity') ?>

    <?php // echo $form->field($model, 'Quantity') ?>

    <?php // echo $form->field($model, 'DamageDescription') ?>

    <?php // echo $form->field($model, 'IdLevelType') ?>

    <?php // echo $form->field($model, 'SpecificLocation') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <?php // echo $form->field($model, 'IdCreateUser') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
