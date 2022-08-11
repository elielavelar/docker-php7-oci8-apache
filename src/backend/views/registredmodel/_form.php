<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Registredmodel */
/* @var $form yii\widgets\ActiveForm */
$url = Yii::$app->getUrlManager()->createUrl('registredmodel');
$tableName = $model->tableName();

$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            NameSpace: $("#$tableName-namespace option:selected").val(),
            CompletePath: $("#$tableName-completepath").val()
        };
    }
JS;
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-3'>
            <?= $form->field($model, 'NameSpace')->dropDownList($model->getNameSpaces(),['readonly' => !$model->isNewRecord,'prompt' => '-Seleccione Espacio de Nombre-','id' => $tableName.'-namespace']) ?>
        </div>
        <div class='col-6'>
            <?= $form->field($model, 'CompletePath')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord, 'id' => $tableName.'-completepath']) ?>
        </div>
        <div class='col-3'>
            <?= $form->field($model, 'EnableExtended')->widget(SwitchInput::class,[
                  'options' => [
                      'id' => $tableName.'-enabledexteded',
                  ],
                  'pluginOptions' => [
                      'onText' => 'Sí',
                      'offText' => 'No',
                  ]
              ]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-6'>
            <?=$form->field($model, 'KeyWord')->widget(Select2::className(),[
                    'size'=> Select2::MEDIUM,
                    'disabled'=> (!$model->isNewRecord),
                    'initValueText'=> $model->KeyWord,
                    'options'=> [
                        'placeholder'=> 'Digite Nombre de Modelo...',
                        'id' => $tableName.'-keyword'
                    ],
                    'pluginOptions'=> [
                        'allowClear'=>TRUE,
                        'minimunInputLength'=> 2,
                        'ajax' => [
                            'url'=> "$url/getmodels",
                            'dataType'=> 'json',
                            'data'=> new JsExpression($resultJS),
                            'cache'=> true,
                            'delay'=> 50,
                        ],
                        'escapeMarkup'=>new JsExpression('function(markup){ return markup; }'),
                        'templateResult'=>new JsExpression('function(data){ return data.text; }'),
                        'templateSelection'=>new JsExpression('function(data){ '
                                . ($model->isNewRecord ? '$("#'.$tableName.'-completepath").val(data.path); ' : '')
                                . 'return data.text; }'),
                    ],
                ]); 
            ?>
        </div>
    </div>
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
      </div>
  </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'],['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
