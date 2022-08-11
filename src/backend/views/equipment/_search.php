<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\search\EquipmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>
        <div class="row">
            <div class="col-3">
                <?= $form->field($model, 'IdResourceType')->widget( Select2::class, [
                        'data' => $model->getResourceTypes(),
                        'options' => ['placeholder' => '--Seleccione Tipo--'],
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                ]) ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'Code') ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'IdServiceCentre')->widget( Select2::class, [
                    'data' => $model->getServiceCentres(),
                    'options' => ['placeholder' => '--Seleccione Centro de Servicio--'],
                    'pluginOptions'=> [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'IdState')->dropDownList( $model->getStates(), [
                        'prompt' => '--Select State--'
                ]) ?>
            </div>
        </div>
        <div class="row"></div>
        <?php // echo $form->field($model, 'CreationDate') ?>

        <?php // echo $form->field($model, 'IdUserCreation') ?>

        <?php // echo $form->field($model, 'LastUpdateDate') ?>

        <?php // echo $form->field($model, 'IdUserLastUpdate') ?>

        <?php // echo $form->field($model, 'IdParent') ?>

        <?php // echo $form->field($model, 'Description') ?>

        <?php // echo $form->field($model, 'TokenId') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
