<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use common\customassets\fileinput\FileInput;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequestdetail */
/* @var $form yii\widgets\ActiveForm */

$controller = 'incidentrequest';
$admin = Yii::$app->customFunctions->userCan($controller.'Admin');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$mainController = Yii::$app->controller->id;
$urlMainController = Yii::$app->getUrlManager()->createAbsoluteUrl($mainController);

$tableName = $model->tableName();
$formName = $tableName.'-form';
$className = \yii\helpers\StringHelper::basename(get_class( $model ));
$csrfParam = Yii::$app->getRequest()->csrfParam;

$attributes = \yii\helpers\Json::encode([ 'results' => $model->getAttributesByActivity()]);
?>
<?php $form = ActiveForm::begin([
    'id' => $formName
]); ?>
<div class="card-body">
    <div class="row">
        <div class="col-4">
            <?= Html::label('Ticket', 'NumTicket')?>
            <?= Html::label($model->incidentRequest->Code, NULL, ['class'=>'form-control readonly'])?>
        </div>
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'RecordDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'Record Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'RecordDate')->textInput(['readonly' => true]);
            ?>
        </div>
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'DetailDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'Detail Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'DetailDate')->textInput(['readonly' => true]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'IdActivityType')->dropDownList($model->getActivityTypes(),[
                'prompt' => '--'.Yii::t('system', 'Select Activity').'--'
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <?= $form->field($model, 'IdIncidentRequestState')->textInput() ?>

    <div class='row'>
        <div class="col-4">
            <?php if($admin):?>
                <?= $form->field($model, 'IdUser')->widget( Select2::class, [
                    'readonly' => ( !$model->isNewRecord || $admin),
                    'initValueText' => ( $model->IdUser ? $model->user->DisplayName : ''),
                    'data'=> $model->getTechnicians(),
                    'options' => ['placeholder' => '--Digite Usuario--'],
                    'pluginOptions'=> [
                        'allowClear' => true,
                    ],
                    'pluginEvents'=> [
                        'change'=> "function(){ }",
                    ],
                ]) ?>
            <?php  else: ?>
                <?= Html::label( ($model->getAttributeLabel('IdUser')), $tableName.'-iduser', [ 'class' => 'control-label']) ?>
                <?= Html::label( ($model->IdUser ? $model->user->DisplayName : ''), $tableName.'-iduser', [ 'class' => 'form-control disabled']) ?>
                <?= $form->field($model, 'IdUser')->hiddenInput()->label(false) ?>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <?php if($model->isNewRecord || $admin ):?>
                <?= $form->field($model, 'IdAssignedUser')->widget( Select2::class, [
                    'readonly' => ( !$model->isNewRecord || $admin),
                    'initValueText' => ( $model->IdAssignedUser ? $model->assignedUser->DisplayName : ''),
                    'data'=>$model->getTechnicians(),
                    'options' => ['placeholder' => '--Digite Usuario--'],
                    'pluginOptions'=> [
                        'allowClear' => true,
                    ],
                    'pluginEvents'=> [
                        'change'=> "function(){ }",
                    ],
                ]) ?>
            <?php  else: ?>
                <?= Html::label( ($model->getAttributeLabel('IdAssignedUser')), $tableName.'-idassigneduser', [ 'class' => 'control-label']) ?>
                <?= Html::label( ($model->IdAssignedUser ? $model->assignedUser->DisplayName : ''), $tableName.'-idassigneduser', [ 'class' => 'form-control disabled']) ?>
                <?= $form->field($model, 'IdAssignedUser')->hiddenInput()->label(false) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Commentaries')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <?php  if($model->isNewRecord):?>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'fileattachment[]')->widget( FileInput::class, [
                    'options'=> [
                        'id'=> "$tableName-fileattachment",
                        'multiple'=> true,
                    ],
                    'pluginOptions' => [
                        'previewFileType' => 'any',
                        'dropZoneEnabled' => false,
                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>



    <?= $form->field($model, 'IdIncidentRequest')->hiddenInput()->label(false); ?>

</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton(
                    '<i class="fas fa-save"></i> '.Yii::t('app', 'Save'),
                    ['class' => 'btn btn-success']) ?>
                <?= Html::a(
                    '<i class="fas fa-times"></i> '.Yii::t('app', 'Cancel'),
                    ['incident/update','id' => $model->IdIncidentRequest ],
                    ['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$script = <<< JS
    var defaultValues = $attributes
    
    $(document).ready(() => {
        $('#$tableName-idactivitytype').on('change', () => getAttributes());
        
        setAttributesForm( defaultValues )
        
    });
JS;
$this->registerJs($script);

$jsHead = <<< JS
    const setAttributesForm = ( data ) => {
        let attributes = Object.entries(data.results)
        $('.form-group').not(':hidden').hide();
        attributes.forEach( attribute => {
            let key = attribute[0]
            let value = attribute[1]
            let controlId = '.field-$tableName-'+(key.toLowerCase());
            let control = $(controlId);
            control.show()
            control.find('.form-group').show();
        })
        
    }
    
    const setAttributesFormAction = ( data ) => {
        const formChildren = document.querySelectorAll('#$formName input, #$formName select, #$formName textarea');
        formChildren.forEach( i => {
            if( i.name !== '$csrfParam'){
                let inputId = ( i.name.replace(']','').split('[') )
                //if( !(data.results.includes( inputId[1]) )){ //works with array
                let key = inputId[1]
                if( !( key in data.results )){ // works with object, aka: json
                    let tag = i.tagName.toLowerCase();
                    let input = $(i);
                    switch (tag){
                        case 'select':
                            //let option = input.find('option:first-child');
                            //input.val( option.val() )
                            i.selectedIndex = 0;
                            break;
                        default:
                            i.value = ''
                    }
                    input.trigger('change')                        
                }  
            }
        })
    }
    
    const getAttributes = () => {
        var id = '$model->Id';
        var type = $('#$tableName-idactivitytype option:selected').val();
        
        AjaxHttpRequest( { 
            url: '$urlMainController/getattributelist',
            datatype: 'json',
            //search: {IdActivityType: type, Id: id },
            method: 'POST',
            formData: true,
            data: (new FormData( document.getElementById('$formName'))),
            success: data => setAttributesForm( data ),
            extra : data => setAttributesFormAction( data )
        } )
    };
JS;
$this->registerJs($jsHead, yii\web\View::POS_END);

?>
