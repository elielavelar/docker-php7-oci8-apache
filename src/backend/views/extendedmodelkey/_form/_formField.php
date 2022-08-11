<?php
/* @var $model \common\models\Extendedmodelfield */
/* @var $form kartik\widgets\ActiveForm */
use kartik\widgets\SwitchInput;
use kartik\widgets\Select2;
$tableName = $model->tableName();
?>
<div class="card-body">
    <?=$form->field($model, 'Id')->hiddenInput(['id' => $tableName.'-id'])->label(false);?>
    <?=$form->field($model, 'IdExtendedModelFieldGroup')->hiddenInput(['id' => $tableName.'-idextendedmodelfieldgroup'])->label(false);?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'IdField')->widget(Select2::className(),[
                        'data'=> $model->getFields(),
                        #'disabled'=> (!$model->isNewRecord),
                        'initValueText'=> ($model->IdField ?  $model->field->Name : ''),
                        'options' => ['placeholder' => '--Seleccione Modelo--','id' => $tableName.'-idfield'],
                        'size'=> Select2::SIZE_MEDIUM,
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                    'pluginEvents'=> [
                        'change'=> "function(){}",
                    ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model,'CustomLabel')->textInput(['maxlength' => true,'id' => $tableName.'-customlabel'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <?=$form->field($model,'Required')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName . '-required',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){}"
                ],
            ])?>
        </div>
        <div class="col-2">
            <?=$form->field($model, 'Sort')->input('number', ['id' => $tableName.'-sort'])?>
        </div>
        <div class="col-4">
            <?=$form->field($model, 'CssClass')->textInput(['id' => $tableName.'-cssclass'])?>
        </div>
        <div class="col-2">
            <?=$form->field($model, 'ColSpan')->input('number', ['id' => $tableName.'-colspan'])?>
        </div>
        <div class="col-2">
            <?=$form->field($model, 'RowSpan')->input('number', ['id' => $tableName.'-rowspan'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model, 'Description')->textarea(['id' => $tableName.'-description','rows' => 4]);?>
        </div>
    </div>
</div>