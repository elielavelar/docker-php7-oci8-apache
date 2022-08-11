<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Servicecentreservices */
/* @var $form yii\widgets\ActiveForm */
$formName = 'task-form';
$prefixName = 'servicetask';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName,
]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true,  'id' => $prefixName.'-name']) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Host')->textInput(['maxlength' => true, 'id' => $prefixName.'-host']) ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'Route')->textInput(['maxlength' => true, 'id' => $prefixName.'-route']) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Port')->input('number', ['id' => $prefixName.'-port']) ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'IdProtocolType')->dropDownList($model->getProtocolTypes(),['id' => $prefixName.'-idprotocoltype']) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(),['id' => $prefixName.'-idtype']) ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id' => $prefixName.'-idstate']) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6, 'id' => $prefixName.'-description']) ?>
      </div>
  </div>
</div>
<?= $form->field($model, 'Id')->hiddenInput(['id' => $prefixName.'-id'])->label(FALSE) ?>
<?= $form->field($model, 'IdService')->hiddenInput(['id' => $prefixName.'-idservice'])->label(FALSE) ?>
<?php ActiveForm::end(); ?>
