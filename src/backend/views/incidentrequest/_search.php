<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\IncidentrequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incidentrequest-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdCategoryType') ?>

    <?= $form->field($model, 'IdSubCategoryType') ?>

    <?= $form->field($model, 'IdServiceCentre') ?>

    <?= $form->field($model, 'IdReportUser') ?>

    <?php // echo $form->field($model, 'RequestDate') ?>

    <?php // echo $form->field($model, 'IncidentDate') ?>

    <?php // echo $form->field($model, 'IdPriorityType') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <?php // echo $form->field($model, 'IdApprovedUser') ?>

    <?php // echo $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'TokenId') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <?php // echo $form->field($model, 'Code') ?>

    <?php // echo $form->field($model, 'IdRejectUser') ?>

    <?php // echo $form->field($model, 'RejectDate') ?>

    <?php // echo $form->field($model, 'ApprovedDate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('system', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('system', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
