<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Securityincident;
use trntv\yii\datetime\DateTimeWidget;
use kartik\form\ActiveField;
use kartik\select2\Select2;
use yii\web\JsExpression;
use common\models\Type;
use common\models\State;
use backend\models\Incident;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */
/* @var $form yii\widgets\ActiveForm */

$urlUsers = Yii::$app->getUrlManager()->createUrl('user');

$tableName = $model->tableName();
$formName = "form-".$tableName;

$options = ['maxlength' => true, 'readonly'=> 'readonly'];

$filterDepartments = Yii::$app->customFunctions->userCan(Securityincident::tableName().'FilterServiceCentre');
$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            idservicecentre: $("#$tableName-idservicecentre").val()
        };
    }
JS;

$sint = NULL;
$sint_model = Type::findOne(['KeyWord'=> StringHelper::basename(Incident::className()).'InterruptType','Code'=> Incident::INTERRUPT_TYPE_WITHOUT]);
if(!empty($sint_model)){
    $sint = $sint_model->Id;
}
?>
<?php $form = ActiveForm::begin(['id' => $formName]); ?>
<div class="card-body">
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'Title')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'Description')->textarea(['rows' => 4]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'IdServiceCentre')->widget(Select2::className(),[
                                    'data'=>$model->getServicecentres(),
                                    'disabled'=> (!$model->isNewRecord),
                                    'initValueText'=> ($model->IdServiceCentre ? $model->serviceCentre->Name:""),
                                    'options' => ['placeholder' => '--Seleccione Departamento--'],
                                    #'size'=>'lg',
                                    'pluginOptions'=> [
                                        'allowClear' => true,
                                    ],
                                'pluginEvents'=> [
                                    'change'=> "function(){}",
                                ],
                        ]);

                    ?>
                </div>
                <div class="col-6">
                    <?php
                        if($filterDepartments || $admin){
                            echo $form->field($model, 'IdReportUser')->widget(Select2::className(),[
                                'size'=> Select2::MEDIUM,
                                'disabled'=> (!$model->isNewRecord),
                                'initValueText'=> ($model->IdReportUser ? $model->reportUser->DisplayName:""),
                                'options'=> [
                                    'placeholder'=> 'Digite Nombre de Usuario...',
                                ],
                                'pluginOptions'=> [
                                    'allowClear'=>TRUE,
                                    'minimunInputLength'=> 2,
                                    'ajax' => [
                                        'url'=> "$urlUsers/getfilteruser",
                                        'dataType'=> 'json',
                                        'data'=> new JsExpression($resultJS),
                                        'cache'=> TRUE,
                                        'delay'=> 250,
                                    ],
                                    'escapeMarkup'=>new JsExpression('function(markup){ return markup; }'),
                                    'templateResult'=>new JsExpression('function(user){ return user.text; }'),
                                    'templateSelection'=>new JsExpression('function(user){ return user.text; }'),
                                ],
                            ]); 
                        } else {
                            echo Html::label('Usuario que Reporta', "$tableName-idreportuser");
                            echo Html::label(($model->IdReportUser ? $model->reportUser->DisplayName:""), NULL, ['class'=>'form-control readonly']);
                            echo $form->field($model, 'IdReportUser')->hiddenInput()->label(FALSE);
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(),['disabled' => in_array($model->state->Code, [Securityincident::STATE_CLOSED])]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'IdLevelType')->dropDownList($model->getLevelTypes(),['disabled' => in_array($model->state->Code, [Securityincident::STATE_CLOSED])]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'IdPriorityType')->dropDownList($model->getPriorityTypes(),['disabled' => in_array($model->state->Code, [Securityincident::STATE_CLOSED])]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'IdInterruptType')->dropDownList($model->getInterruptTypes(),['disabled' => in_array($model->state->Code, [Securityincident::STATE_CLOSED])]) ?>
                </div>
            </div>
            
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'Ticket')->textInput($options) ?>
                </div>
                <div class="col-6">
                    <?= Html::label('Estado', 'NomEstado');?>
                    <?= Html::label(($model->IdState ? $model->state->Name:"" ), NULL, ['class'=>'form-control readonly']);?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $admin ? $form->field($model, 'TicketDate')->widget(DateTimeWidget::className(),[
                        'phpDatetimeFormat' => 'php:d-m-Y H:i',
                        'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                        'options'=> [
                            'readonly'=> TRUE,
                        ],
                        'clientOptions'=> [
                            'locale'=>'ES_es',
                            'ignoreReadonly'=> TRUE,
                            #'maxDate'=> new JsExpression('moment()'),
                            'defaultDate' => new JsExpression("moment('$model->TicketDate','DD-MM-YYYY HH:mm:ss')"),
                        ],
                    ]):$form->field($model, 'TicketDate')->textInput(['readonly'=> TRUE]) ;
                    ?>
                </div>
                <div class="col-6">
                    <?= ($model->isNewRecord || $admin) ? $form->field($model, 'IncidentDate')->widget(DateTimeWidget::className(),[
                        'phpDatetimeFormat' => 'php:d-m-Y H:i',
                        'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                        'options'=> [
                            'readonly'=> TRUE,
                        ],
                        'clientOptions'=> [
                            'locale'=>'ES_es',
                            'ignoreReadonly'=> TRUE,
                            #'maxDate'=> new JsExpression('moment()'),
                            'defaultDate' => new JsExpression("moment('$model->IncidentDate','DD-MM-YYYY HH:mm:ss')"),
                        ],
                    ]):$form->field($model, 'IncidentDate')->textInput(['readonly'=> TRUE]) ;
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'SolutionDate')->textInput(['readonly'=> TRUE]);?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'IdIncident')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'IdCategoryType')->dropDownList($model->getCategoryTypes(),['disabled' => in_array($model->state->Code, [Securityincident::STATE_CLOSED])]) ?>
                </div>
                <div class="col-6">
                    <?= Html::label('Usuario Creación', 'userCreateName') ?>
                    <?= Html::label($model->createUser->DisplayName, NULL, ['class' => 'form-control readonly']) ?>
                    <?= $form->field($model, 'IdCreateUser')->hiddenInput()->label(FALSE); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= Html::label('Usuario Asignado', 'userName') ?>
                    <?= Html::label(($model->IdUser ? $model->user->DisplayName:""), NULL, ['class' => 'form-control readonly','id'=>'userName']) ?>
                    <?= $form->field($model, 'IdUser')->hiddenInput()->label(FALSE); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= ( ($save && !in_array($model->state->Code, [Securityincident::STATE_CLOSED]) ) ? Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) : "");?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
