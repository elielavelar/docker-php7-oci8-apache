<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;
use yii\web\JsExpression;
use backend\models\Securityincident;
use backend\models\Problemtype;
use yii\helpers\StringHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincidentdetails */
/* @var $form yii\widgets\ActiveForm */

$update = Yii::$app->customFunctions->userCan(StringHelper::basename(Securityincident::class).'Update');
$url = $update ? "update/":"";
$admin = Yii::$app->customFunctions->userCan(StringHelper::basename(Securityincident::class).'Admin');
$tableName = $model->tableName();
$urlProblemtypes = Yii::$app->urlManager->createUrl('problemtype');

$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            idactivetype: $("#$tableName-idactivetype").val()
        };
    }
JS;

?>
<?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-12">
                        <?= $form->field($model, 'Description')->textarea(['rows' => 4]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= $form->field($model, 'IdAssignedUser')->widget(Select2::className(),[
                                        'data'=>$model->getUserAssignment(),
                                        'disabled'=> (!$model->isNewRecord),
                                        'initValueText'=> ($model->IdAssignedUser ? $model->assignedUser->DisplayName:""),
                                        'options' => ['placeholder' => '--Seleccione Activo--'],
                                        #'size'=>'lg',
                                        'pluginOptions'=> [
                                            'allowClear' => true,
                                        ],
                                    'pluginEvents'=> [
                                        'change'=> "function(){ }",
                                    ],
                                ]);

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <?= $admin ? $form->field($model, 'DetailDate')->widget(DateTimeWidget::className(),[
                            'phpDatetimeFormat' => 'php:d-m-Y H:i',
                            'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                            'options'=> [
                                'readonly'=> TRUE,
                            ],
                            'clientOptions'=> [
                                'locale'=>'SV_es',
                                'ignoreReadonly'=> TRUE,
                                'maxDate'=> new JsExpression('moment()'),
                            ],
                        ]):$form->field($model, 'DetailDate')->textInput(['readonly'=> TRUE]) ;
                        ?>
                    </div>
                    <div class="col-6">
                        <?= $admin ? $form->field($model, 'RecordDate')->widget(DateTimeWidget::className(),[
                            'phpDatetimeFormat' => 'php:d-m-Y H:i',
                            'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                            'options'=> [
                                'readonly'=> TRUE,
                            ],
                            'clientOptions'=> [
                                'locale'=>'SV_es',
                                'ignoreReadonly'=> TRUE,
                                'maxDate'=> new JsExpression('moment()'),
                            ],
                        ]):$form->field($model, 'RecordDate')->textInput(['readonly'=> TRUE]) ;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <?= $form->field($model, 'IdActivityType')->dropDownList($model->getActivityTypes(),['disabled'=> !$model->isNewRecord]) ?>
                    </div>
                    <div class="col-6">
                        <?= $admin ? $form->field($model, 'SolutionDate')->widget(DateTimeWidget::className(),[
                            'phpDatetimeFormat' => 'php:d-m-Y H:i',
                            'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                            'options'=> [
                                'readonly'=> TRUE,
                            ],
                            'clientOptions'=> [
                                'locale'=>'SV_es',
                                'ignoreReadonly'=> TRUE,
                                #'maxDate'=> new JsExpression('moment()'),
                            ],
                        ]):$form->field($model, 'SolutionDate')->textInput(['readonly'=> TRUE]) ;
                        ?> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= Html::label($model->getAttributeLabel('IdIncidentState'), 'incidentstate') ?>
                        <?= Html::label(($model->IdIncidentState ? $model->incidentState->Name:""), NULL, ['class'=> 'form-control readonly','id'=>'incidentstate']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'Commentaries')->textarea(['rows' => 4]) ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'Investigation')->textarea(['rows' => 4]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'KnowledgeBase')->textarea(['rows' => 4]) ?>
            </div>
        </div>
        <?= $form->field($model, 'IdSecurityIncident')->hiddenInput()->label(FALSE); ?>
        <?= $form->field($model, 'IdUser')->hiddenInput()->label(FALSE); ?>
        <?= $form->field($model, 'IdIncidentState')->hiddenInput()->label(FALSE) ?>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="fas fa-times"></i> Cancelar',['securityincident/'.$url.$model->IdSecurityIncident],['class'=> 'btn btn-danger']);?>
                </span>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>
<?php
$script = <<< JS
    $(document).ready(function(){
        $("#$tableName-idactivitytype").on('change', function(){
            
        });
    });
JS;
$this->registerJs($script);

?>