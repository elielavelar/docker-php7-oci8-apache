<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Zonesupervisors */
/* @var $form yii\widgets\ActiveForm */

$form = ActiveForm::begin([
    'id' => $formName,
]); 
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'IdUser')->widget(Select2::class, [
                'size' => Select2::MEDIUM,
                'id' => $tableName."-iduser",
                #'initValueText' => ($model->IdMember ? $model->member->displayName: ''),
                'data' => $model->getUsers(),
                'options' => [
                    'placeholder' => '--SELECCIONE SUPERVISOR--',
                ],
                'pluginOptions' => [
                     'allowClear' => true,
                ],
                'pluginEvents'=> [
                    'change'=> new JsExpression('function(){}'),
                ],

            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]);?>
        </div>
    </div>
</div>
<?= $form->field($model, 'Id')->hiddenInput()->label(false);?>
<?= $form->field($model, 'IdZone')->hiddenInput()->label(false);?>
<?php ActiveForm::end(); ?>