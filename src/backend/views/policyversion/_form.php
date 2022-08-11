<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;
use kartik\widgets\DateTimePicker;
use yii\web\JsExpression;
\backend\assets\MomentAsset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\Policyversion */
/* @var $modelDetail \common\models\Attachment */
/* @var $form yii\widgets\ActiveForm */

$tableName = $model->tableName();
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'Version')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-1">
            <?= $form->field($model, 'Approved')->widget(SwitchInput::class,[
                'pluginOptions' => [
                    'onText' => 'SI',
                    'offText' => 'NO',
                ],
                'pluginEvents' => [
                     "switchChange.bootstrapSwitch" => new JsExpression("function(i, e) { var inp = $('#$tableName-approveddate'); "
                             . "if(e) { inp.val( moment().format('DD-MM-YYYY HH:mm:ss') );} "
                             . " else { inp.val(''); } "
                             . " }"),
                ],
            ]) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'ApprovedDate')->widget(DateTimePicker::class,[
                'language'=>'es',
                'readonly'=>true,
                'pluginOptions'=> [
                    'format'=>'dd-mm-yyyy h:i:s',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ],
            ]) ?>
        </div>
        <div class="col-1">
            <?= $form->field($model, 'Sent')->widget(SwitchInput::class,[
                'pluginOptions' => [
                    'onText' => 'SI',
                    'offText' => 'NO',
                ],
                'pluginEvents' => [
                     "switchChange.bootstrapSwitch" => new JsExpression("function(i, e) { var inp = $('#$tableName-sentdate'); "
                             . "if(e) { inp.val( moment().format('DD-MM-YYYY HH:mm:ss') );} "
                             . " else { inp.val(''); } "
                             . " }"),
                ],
            ]) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'SentDate')->widget(DateTimePicker::class,[
                'language'=>'es',
                'readonly'=>true,
                'pluginOptions'=> [
                    'format'=>'dd-mm-yyyy h:i:s',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ],
            ]) ?>
        </div>
        <div class="col-2">
            <?= $form->field($model, 'ActualVersion')->widget(SwitchInput::class,[
                'pluginOptions' => [
                    'onText' => 'SI',
                    'offText' => 'NO',
                ],
                'pluginEvents' => [],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6, 'maxlength' => TRUE]) ?>
        </div>
    </div>
    <?php if(!$model->isNewRecord){?>
    <div class="row">
        <div class="col-12">
            <span class="float-left">
                <?php if($model->attachment){
                    echo Html::a('<i class="fas fa-download"></i> Descargar Archivo',$model->attachment->path
                            , ['class'=> 'btn btn-success','target' => '_blank']);
                } ?>
                <?= Html::button('<i class="fas fa-upload"></i> Cargar Nuevo Archivo',['type'=> 'button','id' => 'btn-LoadFile','class'=> 'btn btn-primary']); ?>
            </span>
            <span class="float-right">
                <?php if($model->attachment){
                    echo Html::a('<i class="fas fa-trash"></i> Eliminar Archivo', "javascript:void(0)"
                        , ['class'=> 'btn btn-danger' , 'id' => 'btn-delete', 'data' => ['attachment' => $model->attachment->Id]]);
                } ?>
            </span>
        </div>
    </div>
    <?php } ?>
    <?= $form->field($model, 'IdPolicy')->hiddenInput()->label(FALSE) ?>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['policy/'.$model->IdPolicy],['class'=> 'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>