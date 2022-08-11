<?php
/* @var $model \common\models\Fieldscatalog */
/* @var $form kartik\widgets\ActiveForm */
$tableName = $model->tableName();

?>
<div class="card-body">
    <?=$form->field($model, 'Id')->hiddenInput(['id' => $tableName.'-id'])->label(false);?>
    <?=$form->field($model, 'IdField')->hiddenInput(['id' => $tableName.'-idfield'])->label(false);?>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model,'Name')->textInput(['id' => $tableName.'-name', 'maxlength' => true])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model,'Value')->textInput(['id' => $tableName.'-value', 'maxlength' => true])?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?=$form->field($model,'Sort')->input('number',['id' => $tableName.'-sort', 'maxlength' => true])?>
        </div>
        <div class="col-6">
            <?=$form->field($model,'IdState')->dropDownList($model->getStates(),['id' => $tableName.'-sort', 'maxlength' => true])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?=$form->field($model, 'Description')->textarea(['id' => $tableName.'-description','rows' => 4]);?>
        </div>
    </div>
</div>