<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IncidentdetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incidentdetail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdIncident') ?>

    <?= $form->field($model, 'Title') ?>

    <?= $form->field($model, 'Description') ?>

    <?= $form->field($model, 'DetailDate') ?>

    <?php // echo $form->field($model, 'RecordDate') ?>

    <?php // echo $form->field($model, 'InSiteDate') ?>

    <?php // echo $form->field($model, 'SolutionDate') ?>

    <?php // echo $form->field($model, 'IdSupportType') ?>

    <?php // echo $form->field($model, 'IdProblemType') ?>

    <?php // echo $form->field($model, 'IdActivityType') ?>

    <?php // echo $form->field($model, 'IdEvaluatorUser') ?>

    <?php // echo $form->field($model, 'IdEvaluationValue') ?>

    <?php // echo $form->field($model, 'IdIncidentState') ?>

    <?php // echo $form->field($model, 'TicketProv') ?>

    <?php // echo $form->field($model, 'CodEquipment') ?>

    <?php // echo $form->field($model, 'IdAssignedUser') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <?php // echo $form->field($model, 'Commentaries') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
