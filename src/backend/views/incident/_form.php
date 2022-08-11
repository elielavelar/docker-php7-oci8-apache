<?php

use kartik\form\ActiveForm;
use common\customassets\helpers\Html;
use kartik\widgets\DateTimePicker;
use common\customassets\fileinput\FileInput;
use kartik\select2\Select2;
use yii\web\JsExpression;
use backend\models\Incident;
use common\models\Type;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Incident */
/* @var $modelTitle backend\models\Incidenttitle */
/* @var $filterDepartments boolean */

$admin = Yii::$app->customFunctions->userCan('incidentAdmin');
$urlUsers = Yii::$app->getUrlManager()->createUrl('user');
$urlIncidentCategory = Yii::$app->getUrlManager()->createAbsoluteUrl('incidentcategory');
$urlTitles = Yii::$app->getUrlManager()->createAbsoluteUrl('incidenttitle');
$urlResources = Yii::$app->getUrlManager()->createAbsoluteUrl('resource');

$tableName = $model->tableName();
$formName = 'form-'.$tableName;
$edit = ($model->isNewRecord || empty($model->incidentdetails));

$tableNameTitle = $modelTitle->tableName();
$modalTitle = $tableNameTitle.'-modal';

$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            idservicecentre: $("#$tableName-idservicecentre").val()
        };
    }
JS;
$categoryResultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            idparent: document.getElementById("$tableName-idcategorytype").value
        };
    }
JS;
$resourceResultJS = <<< JS
    (params) => {
        return {
            q: params.term, 
            IdServiceCentre: document.getElementById("$tableName-idservicecentre").value
        };
    }
JS;
$sint = NULL;
$sint_model = Type::findOne(['KeyWord'=> StringHelper::basename(Incident::class).'Interrupt','Code'=> Incident::INTERRUPT_TYPE_WITHOUT]);
if(!empty($sint_model)){
    $sint = $sint_model->Id;
}

?>
<?php $form = ActiveForm::begin([
    'id'=> $formName,
]);
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= $form->field($model ,'IdResource')->widget( Select2::class, [
                'size'=> Select2::MEDIUM,
                'disabled'=> (!$model->isNewRecord && !$admin),
                'initValueText'=> ($model->IdResource ? $model->resource->label :""),
                'options'=> [
                    'placeholder'=> 'Digite Código Recurso...',
                ],
                'pluginOptions'=> [
                    'allowClear'=>TRUE,
                    'minimunInputLength'=> 2,
                    'ajax' => [
                        'url'=> "$urlResources/getfilter",
                        'dataType'=> 'json',
                        'data'=> new JsExpression($resourceResultJS),
                        'cache'=> true,
                        'delay'=> 50,
                    ],
                    'escapeMarkup'=>new JsExpression('function(markup){ return markup; }'),
                    'templateResult'=>new JsExpression('function( data ){ return data.text; }'),
                    'templateSelection'=>new JsExpression('function( data ){ return data.text; }'),
                ],
            ])?>
        </div>
    </div>
    <div class="row">
        <div class="col-11">
            <?= Html::label($model->getAttributeLabel('IdTitle'), "$tableName-idtitle");?>
            <?php
            if($model->isNewRecord || $admin){
                echo $form->field($model, 'IdTitle')->widget(Select2::class,[
                    'data'=>$model->getTitles(),
                    'disabled'=> (!$model->isNewRecord && !$admin),
                    'initValueText'=> ($model->IdTitle ? $model->title->Title:""),
                    'options' => ['placeholder' => '--Seleccione Título--'],
                    #'size'=>'lg',
                    'pluginOptions'=> [
                        'allowClear' => true,
                    ],
                    'pluginEvents'=> [

                    ],
                ])->label(FALSE);
            } else {
                echo Html::label(($model->IdTitle ? $model->title->Title:""),NULL,['class'=>'form-control readonly']);
                echo $form->field($model, 'IdTitle')->hiddenInput()->label(FALSE);
            }
            ?>
        </div>
        <?php if($model->isNewRecord || $admin):?>
            <div class="col-1">
                <div class="form-group">
                    <?= Html::label('&nbsp;', 'btn-title', ['class' => ''])?>
                    <?= Html::button(
                        Html::icon('fas fa-plus-circle'),
                        [
                            'class'=> 'btn btn-primary btn-lg' ,
                            'id' => 'btn-title',
                            'style' => ['display' => 'block']
                        ]
                    )?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'Ticket')->textInput(['readonly'=> TRUE]) ?>
        </div>
        <div class="col-3">
            <?= ($model->isNewRecord || $admin) ? $form->field($model, 'TicketDate')->widget(
                DateTimePicker::class, [
                'language' => 'es',
                'readonly' => true,
                'options' => ['placeholder' => 'dd-mm-yyyy hh:ii'],
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
            : $form->field($model, 'TicketDate')->textInput(['readonly'=> true]) ;
            ?>
        </div>
        <div class="col-3">
            <?= ($model->isNewRecord || $admin) ? $form->field($model, 'IncidentDate')->widget(
                DateTimePicker::class, [
                'language' => 'es',
                'readonly' => true,
                'options' => ['placeholder' => 'dd-mm-yyyy hh:ii'],
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
            : $form->field($model, 'IncidentDate')->textInput(['readonly'=> TRUE]) ;
            ?>
        </div>

        <div class="col-3">
            <?= Html::label('Estado', 'NomEstado');?>
            <?= Html::label(($model->IdState ? $model->state->Name:"" ), NULL, ['class'=>'form-control readonly']);?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= Html::label('Duicentro / Departamento que Reporta', "$tableName-idservicecentre");?>
            <?php
                if($filterDepartments || $admin){
                    echo $form->field($model, 'IdServiceCentre')->widget(Select2::class,[
                            'data'=>$model->getServicecentres(),
                            'disabled'=> (!$model->isNewRecord && !$admin),
                            'initValueText'=> ($model->IdServiceCentre ? $model->serviceCentre->Name:""),
                            'options' => ['placeholder' => '--Seleccione Departamento--'],
                            #'size'=>'lg',
                            'pluginOptions'=> [
                                'allowClear' => true,
                            ],
                        'pluginEvents'=> [
                            'change'=> "function(){ $('#$tableName-idreportuser').empty(); }",
                        ],
                        ])->label(FALSE);
                } else {
                    echo Html::label(($model->IdServiceCentre ? $model->serviceCentre->Name:""),NULL,['class'=>'form-control readonly']);
                    echo $form->field($model, 'IdServiceCentre')->hiddenInput()->label(FALSE);
                }
            ?>
        </div>
        <div class="col-6">
            <?php
                if($filterDepartments || $admin){
                    echo $form->field($model, 'IdReportUser')->widget(Select2::class,[
                        'size'=> Select2::MEDIUM,
                        'disabled'=> (!$model->isNewRecord && !$admin),
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
                    echo Html::label(($model->IdReportUser ? $model->reportUser->completeName:""), NULL, ['class'=>'form-control readonly']);
                    echo $form->field($model, 'IdReportUser')->hiddenInput()->label(FALSE);
                }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="col-12">
                <?= $form->field($model, 'IdCategoryType')->dropDownList($model->getCategoryTypes(),
                    [
                        'prompt'=>'--Seleccione Tipo de Incidencia--'
                    ]) ?>
            </div>
            <div class="col-12">
                <?php
                if($admin || $model->isNewRecord ){
                    echo $form->field($model, 'IdSubCategoryType')->dropDownList([],[
                        'prompt'=>'--Seleccione Sub Tipo de Incidencia--'
                    ]);
                } else {
                    echo Html::label($model->getAttributeLabel('IdSubCategoryType'), "$tableName-idsubcategorytype");
                    echo Html::label(($model->IdSubCategoryType ? $model->subCategoryType->Name :""), null, ['class'=>'form-control readonly']);
                    echo $form->field($model, 'IdSubCategoryType')->hiddenInput()->label(false);
                }
                ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'IdPriorityType')->dropDownList($model->getPriorityTypes(),[]) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'IdRevisionType')->dropDownList($model->getRevisionTypes(),['prompt'=>'--Tipo de Revisión--']); ?>
            </div>
            <div class="col-12">
                <?= Html::label('Asignar a Usuario:','IdUser') ?>
                <?php
                    if($filterDepartments || $admin){
                        echo $form->field($model, 'IdUser')->widget(Select2::class,[
                                'data'=>$model->getTechnicians(),
                                'disabled'=> (!$model->isNewRecord),
                                'initValueText'=> ($model->IdUser ? $model->user->completeName:""),
                                'options' => ['placeholder' => '--Digite Usuario--'],
                                #'size'=>'lg',
                                'pluginOptions'=> [
                                    'allowClear' => true,
                                ],
                            'pluginEvents'=> [
                                'change'=> "function(){ }",
                            ],
                            ])->label(FALSE);
                    } else {
                        echo  Html::label(($model->IdUser ? $model->user->completeName:""),NULL, ['class'=>'form-control readonly']);
                        echo $form->field($model, 'IdUser')->hiddenInput()->label(FALSE);
                    }
                ?>
            </div>
            <div class="col-12">
                <?= Html::label($model->getAttributeLabel('IdCreateUser'),'IdCreateUser') ?>
                <?php
                    echo  Html::label(($model->IdCreateUser ? $model->createUser->completeName:""),NULL, ['class'=>'form-control readonly']);
                    echo $form->field($model, 'IdCreateUser')->hiddenInput()->label(FALSE);
                ?>
            </div>
        </div>
        <div class="col-6">
            <div class="col-12">
                <?= $form->field($model, 'Commentaries')->textarea(['maxlength'=>true,'rows'=> 6]) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'IdInterruptType')->dropDownList($model->getInterruptTypes(),['prompt'=>'--Seleccione Tipo de Interrupción--']) ?>
            </div>
            <div class="col-12 interruptdate" style="<?= ($model->IdInterruptType ? ($model->interruptType->Code == Incident::INTERRUPT_TYPE_WITHOUT ? 'display:none':''):'display:none')?>">
                <?= ($model->isNewRecord || $admin) ? $form->field($model, 'InterruptDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => 'dd-mm-yyyy hh:ii'],
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
                    : $form->field($model, 'InterruptDate')->textInput(['readonly'=> true]) ;
                ?>
            </div>
            <?php if(($model->IdState ? (in_array($model->state->Code, [Incident::STATE_SOLVED, Incident::STATE_CLOSED])):FALSE)){ ?>
            <div class="col-12">
                <?= ($admin || $filterDepartments ) ?
                    $form->field($model, 'SolutionDate')->widget(
                            DateTimePicker::class, [
                        'language' => 'es',
                        'readonly' => true,
                        'options' => ['placeholder' => Yii::t('system', 'Solution Date')],
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
                    : $form->field($model, 'SolutionDate')->textInput();
                ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php  if($model->isNewRecord):?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'fileattachment[]')->widget( FileInput::class, [
                'id'=> 'fileattachment',
                'language' => 'es',
                'options'=> [
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
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= ($model->isNewRecord || $admin ) ? Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']): '' ; ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=>'btn btn-danger'])?>
            </span>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?= $this->render('_form/_titleModal', ['model' => $modelTitle])?>
<?php
$script = <<< JS
   $(document).ready(function(){
       $('#btn-title').on('click', () => {
           $('#$modalTitle').modal();
       })
       
        $("#$tableName-idinterrupttype").on('change',function(){
            var _type = $("#$tableName-idinterrupttype option:selected").val();
            if(parseInt(_type) != $sint){
                $(".interruptdate").show();
            } else {
                $(".interruptdate").hide();
                $("#$tableName-interruptdate").empty();
            }
        });
        
        document.getElementById('$tableName-idcategorytype').addEventListener("change", () => {
            let _type = document.getElementById("$tableName-idcategorytype");
            let _option = _type.options[_type.selectedIndex].value
            fetchSubcategories( _option );
        });
   });
JS;
$this->registerJs($script, yii\web\View::POS_READY);

$jsHead = <<< JS
    const fetchSubcategories = function (idparent, selected = null){
        let url = new URL('$urlIncidentCategory/getlist') 
        let params= { idparent: idparent }
        var select = $('#$tableName-idsubcategorytype');
        var firstOpt = select.find(':first-child');
        select.empty()
            .append( firstOpt );
        url.search = new URLSearchParams( params ).toString();
        fetch( url )
            .then( response => response.json() ) 
            .then( data => {
                data.results.forEach( option => {
                    let opt = $('<option></option>');
                    opt.val( option.id);
                    opt.html( option.text );
                    if( selected !== null && parseInt(selected) === parseInt( option.id )){
                        opt.attr('selected', true);
                    }
                    select.append( opt )
                })
            } ) 
    };

    const fetchIncidentTitles = function (idcategory = null, selected = null){
        let url = new URL('$urlTitles/getlist') 
        let params = {}
        idcategory ? params.idcategory = idcategory:null;
        url.search = new URLSearchParams( params ).toString();
        var select = $('#$tableName-idtitle');
        var firstOpt = select.find(':first-child');
        select.empty()
            .append( firstOpt );
        fetch( url )
            .then( response => response.json() ) 
            .then( data => {
                data.results.forEach( option => {
                    let opt = $('<option></option>');
                    opt.val( option.id);
                    opt.html( option.text );
                    if( selected !== null && parseInt(selected) === parseInt( option.id )){
                        opt.attr('selected', true);
                    }
                    select.append( opt )
                })
            } ) 
         select.trigger('change');
    };
JS;
$this->registerJs($jsHead, yii\web\View::POS_END);

?>