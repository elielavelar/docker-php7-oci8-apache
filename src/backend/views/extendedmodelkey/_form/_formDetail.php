<?php
/* @var $model \common\models\Extendedmodelfieldgroup */
/* @var $form kartik\widgets\ActiveForm */
use kartik\widgets\SwitchInput;
$tableName = $model->tableName();
?>
<div class="card-body">
    <?=$form->field($model, 'Id')->hiddenInput(['id' => $tableName.'-id'])->label(false);?>
    <?=$form->field($model, 'IdExtendedModelKey')->hiddenInput(['id' => $tableName.'-idextendedmodelfield'])->label(false);?>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model,'Name')->textInput(['maxlength' => true,'id' => $tableName.'-name'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?=$form->field($model, 'Sort')->input('number', ['id' => $tableName.'-sort'])?>
        </div>
        <div class="col-4">
            <?= $form->field($model,'VisibleContainer')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName.'-visiblecontainer',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){}"
                ],
            ]);
            ?>
        </div>
        <div class="col-4">
            <?=$form->field($model, 'IdState')->dropDownList($model->getStates(), ['id' => $tableName.'-idstate'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model, 'Description')->textarea(['id' => $tableName.'-description','rows' => 4]);?>
        </div>
    </div>
</div>