<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Servicetaskcustomstate */
/* @var $form yii\widgets\ActiveForm */
$formName = 'state-form';
$prefixName = 'servicetaskcustomstate';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName,
]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'DateStart')->widget(DatePicker::className(), [
                'readonly' => true,
                'language' => 'es',
                'type' => DatePicker::TYPE_INPUT,
                'options' => [
                    'placeholder' => 'Fecha de Inicio...',
                    'id' => $prefixName.'-datestart',
                ],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
                
            ]);
            ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'DateEnd')->widget(DatePicker::className(), [
                'language' => 'es',
                'readonly' => TRUE,
                'type' => DatePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'Fecha de Fin...'
                    ,'id' => $prefixName.'-dateend'],
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);
            ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Active')->widget(SwitchInput::class, [
              'options' => [
                  'id' => $prefixName.'-active',
              ],
          ])?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id' => $prefixName.'-idstate']) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 4, 'id' => $prefixName.'-description']) ?>
      </div>
  </div>
    <div class="row">
      <div class='col-6'>
          <?= $form->field($model, 'userCreateName')->textInput(['id' => $prefixName.'-usercreatename', 'disabled' => true]) ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'userDisableName')->textInput(['id' => $prefixName.'-userdisablename','disabled' => true]) ?>
      </div>
  </div>
    
</div>
<?= $form->field($model, 'Id')->hiddenInput(['id' => $prefixName.'-id'])->label(FALSE) ?>
<?= $form->field($model, 'IdServiceTask')->hiddenInput(['id' => $prefixName.'-idservicetask'])->label(FALSE) ?>
<?= $form->field($model, 'IdUserCreate')->hiddenInput(['id' => $prefixName.'-idusercreate'])->label(FALSE) ?>
<?= $form->field($model, 'IdUserDisabled')->hiddenInput(['id' => $prefixName.'-iduserdisabled'])->label(FALSE) ?>
<?php ActiveForm::end(); ?>
