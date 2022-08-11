<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IncidentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incident-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Ticket') ?>

    <?= $form->field($model, 'IncidentDate') ?>

    <?= $form->field($model, 'TicketDate') ?>

    <?= $form->field($model, 'InterruptDate') ?>

    <?php // echo $form->field($model, 'SolutionDate') ?>

    <?php // echo $form->field($model, 'Title') ?>

    <?php // echo $form->field($model, 'IdServiceCentre') ?>

    <?php // echo $form->field($model, 'IdReportUser') ?>

    <?php // echo $form->field($model, 'IdCategoryType') ?>

    <?php // echo $form->field($model, 'IdInterruptType') ?>

    <?php // echo $form->field($model, 'IdPriorityType') ?>

    <?php // echo $form->field($model, 'IdRevisionType') ?>

    <?php // echo $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'Commentaries') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
