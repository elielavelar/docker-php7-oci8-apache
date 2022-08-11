<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Policy */
/* @var $form yii\widgets\ActiveForm */

?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-8">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'Code')->textInput(['maxlength'=> true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'IdProcess')->widget(Select2::className(),[
                            'data'=>$model->getProcesses(),
                            #'disabled'=> (!$model->isNewRecord),
                            'initValueText'=> ($model->IdProcess ? $model->process->Name:""),
                            'options' => ['placeholder' => '--Seleccione Departamento--'],
                            'size'=> Select2::SIZE_MEDIUM,
                            'pluginOptions'=> [
                                'allowClear' => true,
                            ],
                        'pluginEvents'=> [
                            'change'=> "function(){ }",
                        ],
                ]);
            ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(),['prompt'=>'--Seleccione tipo--']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <?= $form->field($model, 'IdUser')->hiddenInput()->label(FALSE);?>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']); ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger']); ?>
            </span>
        </div>
    </div>
    
</div>

    <?php ActiveForm::end(); ?>
