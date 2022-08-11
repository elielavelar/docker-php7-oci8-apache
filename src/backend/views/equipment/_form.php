<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'IdResourceType')->dropDownList( $model->getResourceTypes(), [
              'prompt' => '--'.(
                  Yii::t('app', '{action} {entity}', [
                      'action' => Yii::t('app', 'Select'),
                      'entity' => Yii::t('app', 'Type'),
                  ])
                  ).'--'
          ]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdServiceCentre')->dropDownList( $model->getServiceCentres(), [
              'prompt' => '--'.(
                  Yii::t('app', '{action} {entity}', [
                      'action' => Yii::t('app', 'Select'),
                      'entity' => Yii::t('system', 'Servicecentre'),
                  ])
                  ).'--'
          ]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'IdState')->dropDownList( $model->getStates(), []) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-3'>
          <?= $form->field($model, 'CreationDate')->widget(
              DateTimePicker::class, [
              'language' => 'es',
              'readonly' => true,
              'options' => ['placeholder' => 'dd-mm-yyyy hh:ii'],
              'pluginOptions' => [
                  'format' => 'dd-mm-yyyy hh:ii',
                  'todayHighlight' => true,
                  'autoclose' => true,
                  'minuteStep' => 1,
              ],
              'pluginEvents' => [
                  'changeDate' => "function(e){  }",
              ],
          ])?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'LastUpdateDate')->widget(
              DateTimePicker::class, [
              'language' => 'es',
              'readonly' => true,
              'options' => ['placeholder' => 'dd-mm-yyyy hh:ii'],
              'pluginOptions' => [
                  'format' => 'dd-mm-yyyy hh:ii',
                  'todayHighlight' => true,
                  'autoclose' => true,
                  'minuteStep' => 1,
              ],
              'pluginEvents' => [
                  'changeDate' => "function(e){  }",
              ],
          ])?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdUserCreation')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdUserLastUpdate')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdParent')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'TokenId')->textInput(['disabled' => true]) ?>
      </div>
  </div>
</div>
<?php
echo "Details:";
var_dump($model->details)?>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> '.Yii::t('app', 'Cancel'), ['index'],['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?= $form->field($model, 'IdType')->hiddenInput()->label(false) ?>
<?php ActiveForm::end(); ?>
