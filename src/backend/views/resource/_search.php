<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ResourceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resource-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdType') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'IdResourceType') ?>

    <?php // echo $form->field($model, 'IdServiceCentre') ?>

    <?php // echo $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'CreationDate') ?>

    <?php // echo $form->field($model, 'IdUserCreation') ?>

    <?php // echo $form->field($model, 'LastUpdateDate') ?>

    <?php // echo $form->field($model, 'IdUserLastUpdate') ?>

    <?php // echo $form->field($model, 'IdParent') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('system', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('system', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
