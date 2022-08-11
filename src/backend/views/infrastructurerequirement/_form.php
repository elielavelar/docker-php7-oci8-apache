<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;
use kartik\form\ActiveField;
use kartik\select2\Select2;
use yii\web\JsExpression;
use common\models\Type;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirement */
/* @var $form yii\widgets\ActiveForm */

$controller = Yii::$app->controller->id;
$tableName = $model->tableName();
$formName = "form-".$tableName;

$permission = (!isset($permission) ? [] : $permission);
$filterDepartment = isset($permission['filterDepartment']) ? $permission['filterDepartment']: false;
$admin = isset($permission['admin']) ? $permission['admin']: false;
$update = isset($permission['update']) ? $permission['update']: false;
$delete = isset($permission['delete']) ? $permission['delete']: false;
$save = isset($permission['save']) ? $permission['save']: false;

$options = ['maxlength' => true, 'readonly'=> 'readonly'];

$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            idservicecentre: $("#$tableName-idservicecentre").val()
        };
    }
JS;
?>
<?php $form = ActiveForm::begin([
    'id' => $formName,
]); ?>
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
                    <?= $form->field($model, 'DamageDescription')->textarea(['rows' => 4]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'SpecificLocation')->textarea(['rows' => 4]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'Description')->textarea(['rows' => 4]) ?>
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
                    <?= ($model->isNewRecord || $admin) ? $form->field($model, 'RequirementDate')->widget(DateTimeWidget::className(),[
                        'phpDatetimeFormat' => 'php:d-m-Y H:i',
                        'momentDatetimeFormat' => 'DD-MM-YYYY HH:mm',
                        'options'=> [
                            'readonly'=> TRUE,
                        ],
                        'clientOptions'=> [
                            'locale'=>'ES_es',
                            'ignoreReadonly'=> TRUE,
                            #'maxDate'=> new JsExpression('moment()'),
                            'defaultDate' => new JsExpression("moment('$model->RequirementDate','DD-MM-YYYY HH:mm:ss')"),
                        ],
                    ]):$form->field($model, 'RequirementDate')->textInput(['readonly'=> TRUE]) ;
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
                    <?php 
                        if($filterDepartment){
                            echo $form->field($model, 'IdServiceCentre')->widget(Select2::className(),[
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
                        } else {
                            echo Html::label($model->getAttributeLabel('IdServiceCentre'), "$tableName-idservicecentre");
                            echo Html::label(($model->IdServiceCentre ? $model->serviceCentre->Name:""), NULL, ['class'=>'form-control readonly']);
                            echo $form->field($model, 'IdServiceCentre')->hiddenInput()->label(FALSE);
                        }
                    ?>
                </div>
                <div class="col-6">
                    <?php
                        if($admin){
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
                    <?= $form->field($model, 'IdInfrastructureRequirementType')->widget(Select2::className(),[
                                'size'=> Select2::MEDIUM,
                                'data' => $model->getInfrastructureRequirementTypes(),
                                'disabled'=> (!$model->isNewRecord),
                                'initValueText'=> ($model->IdInfrastructureRequirementType ? $model->infrastructureRequirementType->Name:""),
                                'options'=> [
                                    'placeholder'=> '--TIPO REQUERIMIENTO--',
                                ],
                                'pluginOptions'=> [
                                    'allowClear'=>$model->isNewRecord,
                                ]
                            ]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'IdPriorityType')->dropDownList($model->getpriorityTypes(),['disabled' => in_array($model->state->Code, [$model::STATE_CLOSED])]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <?=$form->field($model, 'Quantity')->input('number', []); ?>
                </div>
                <div class="col-5">
                    <?=$form->field($model, 'AffectsFunctionality')->dropDownList([$model::AFFECTS_FUNCTIONALITY_DISABLE => 'No', $model::AFFECTS_FUNCTIONALITY_ENABLE => 'Sí'])?>
                </div>
                <div class="col-5">
                    <?=$form->field($model, 'AffectsSecurity')->dropDownList([$model::AFFECTS_SECURITY_DISABLE => 'No', $model::AFFECTS_SECURITY_ENABLE => 'Sí'])?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= Html::label('Usuario Asignado', 'userName') ?>
                    <?= Html::label(($model->IdUser ? $model->user->DisplayName:""), NULL, ['class' => 'form-control readonly','id'=>'userName']) ?>
                    <?= $form->field($model, 'IdUser')->hiddenInput()->label(FALSE); ?>
                </div>
                <div class="col-6">
                    <?= Html::label('Usuario Creación', 'userCreateName') ?>
                    <?= Html::label(($model->IdCreateUser ? $model->createUser->DisplayName : ''), NULL, ['class' => 'form-control readonly']) ?>
                    <?= $form->field($model, 'IdCreateUser')->hiddenInput()->label(FALSE); ?>
                </div>
            </div>
            <?php if(!$model->isNewRecord ? ($model->IdPriorityType ? $model->priorityType->Code == $model::PRIORITY_HIGH : false):false):?>
            <div class="row">
                <div class="col-6">
                    <?= Html::label('Usuario Verificación', 'userVerificationName') ?>
                    <?= Html::label(($model->IdVerificationUser ? $model->verificationUser->DisplayName:""), NULL, ['class' => 'form-control readonly','id'=>'userName']) ?>
                    <?= $form->field($model, 'IdVerificationUser')->hiddenInput()->label(FALSE); ?>
                </div>
                <div class="col-3">
                    <?= $form->field($model, 'VerificationDate')->textInput(['readonly' => true])?>
                </div>
                <div class="col-3">
                    <?= Html::label('Verificado', 'VerificationStatus') ?>
                    <?= Html::label(($model->VerificationStatus == $model::VERIFICATION_STATUS_ENABLE ? 'Sí':'No'), NULL, ['class' => 'form-control readonly','id'=>'userName']) ?>
                    <?= $form->field($model, 'VerificationStatus')->hiddenInput()->label(false)?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= ( ($save && !in_array($model->state->Code, [$model::STATE_CLOSED]) ) ? Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) : "");?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
