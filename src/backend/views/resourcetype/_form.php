<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Resourcetype */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), [
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
      <div class='col-4'>
          <?= $form->field($model, 'KeyWord')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdState')->dropDownList( $model->getStates(), []) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'AgroupationType')->textInput() ?>
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
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton(Yii::t('system', 'Guardar'), ['class' => 'btn btn-success']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
