<?php

use common\customassets\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <?=$form->field($model, 'IdServiceCentre')->widget( Select2::class, [
                        'data' => $model->getServiceCentres(),
                        'options' => ['placeholder' => '--Seleccione Departamento--'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                ])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'IdProfile')->widget( Select2::class, [
                        'data' => $model->getProfiles(),
                        'options' => ['placeholder' => '--Seleccione Perfil--'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                ])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'IdState')->widget( Select2::class, [
                        'data' => $model->getStates(),
                        'options' => ['placeholder' => '--Seleccione Estado--'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                ])?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="form-group">
            <?= Html::submitButton( Yii::t('app', '{icon} {action}', [
                    'icon' => Html::icon('fas fa-search'),
                    'action' => Yii::t('app', 'Search'),
            ]), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', '{icon} {action}', [
                'icon' => Html::icon('fas fa-times'),
                'action' => Yii::t('app', 'Reset'),
            ]), ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
