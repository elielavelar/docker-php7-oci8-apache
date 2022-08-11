<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;

use frontend\models\Citizen;
use common\models\State;
use common\models\Type;
use common\models\Servicecentres;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model \frontend\models\Citizen */
/* @var $modelDetail common\models\Appointments */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail common\models\AppointmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>  StringHelper::basename($modelDetail->className())]), 'Id', 'Name');
$filterType = ArrayHelper::map(Type::findAll(['KeyWord'=>'Process']), 'Id', 'Name');
$filterServicecentre = ArrayHelper::map(Servicecentres::findAll([
                'IdState'=>  State::findOne(['KeyWord'=> StringHelper::basename(Servicecentres::className()),'Code'=>  Servicecentres::STATE_ACTIVE])->Id
                ,'IdType'=> Type::findOne(['KeyWord'=> StringHelper::basename(Servicecentres::className()), 'Code'=> Servicecentres::TYPE_DUISITE
        ])->Id]), 'Id', 'Name');
$urlDetail = \Yii::$app->getUrlManager()->createUrl('appointment');
$tableName = $modelDetail->tableName();
$formName = $tableName."-form";

?>
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">Registrar Cita</h2>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?=$this->render('_detailform', ['model'=>$modelDetail])?>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <span class="pull-right">
                                    <button type="button" id="btnSaveDetail" name="btnSaveDetail" class="btn btn-success">
                                        <i class="fa fa-save"></i> Guardar
                                    </button>
                                    <button type="button" id="btnCancelDetail" name="btnCancelDetail" class="btn btn-danger">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <?php Pjax::begin([
                    'id'=>'details'
                ]); ?>    
                <?= GridView::widget([
                'id'=>'dtgrid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>TRUE,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'Code',
                    [
                        'attribute'=>'ShortCode',
                        'content'=>function($model){
                            return $model->ShortCode ? $model->ShortCode:'-';
                        },
                    ],
                    [
                        'attribute'=>'IdServiceCentre',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdServiceCentre', $filterServicecentre, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdServiceCentre != 0 ? $data->serviceCentre->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    ['attribute'=>'AppointmentDate',
                        'filterType'=> GridView::FILTER_DATE,
                                'filterWidgetOptions'=> [
                                    'language'=>'es',
                                    'readonly'=>true,
                                    'pluginOptions'=> [
                                        'format'=>'dd-mm-yyyy',
                                        'autoclose'=>true,
                                        'todayHighlight' => true,
                                    ],
                                ],
                                'format' => 'html'
                        ],
                    'AppointmentHour',
                    [
                        'attribute'=>'IdState',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdType',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdType', $filterType, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdType != 0 ? $data->type->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{edit} {cancel} {reschedule}',
                        'buttons'=>[
                            'edit' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', "javascript:getDetail($model->Id);", [
                                            'title' => Yii::t('app', 'lead-edit'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'lead-delete'), 
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?></div>
        </div>
    </div>
</div>
<div class="modal fade in" id="modal-detail" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Cita <div class="inline" id="OrderQuestion"></div></strong></h3>
            </div>
            <div class="modal-body">
                <?=$this->render('_detailform', ['model'=>$modelDetail])?>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    $(document).ready(function(){
        
        $('#modal-time').on('hidden.bs.modal', function () {
            
        });
        
        $("#btnCancelDetail").on('click',function(){
            clearDetailForm();
        });
        
        $("#btnSaveDetail").on('click',function(){
            $("#$formName").submit();
        });
    
        
        $("#$formName").on('beforeSubmit',function(){
            $.ajax({
                url: "$urlDetail/save",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {		
                    //var data = JSON.parse(data);
                    if(data.success == true)
                    {
                        clearDetailForm();
                        swal({html:true,title:data.subject+" de Cita", text: data.message, type:"success"},function(){
                            refreshGrid();
                        });
                    }
                    else
                    {
                        swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                        if(data.errors){
                            var errors = {};
                            errors.ID = "$formName";
                            errors.PREFIX = "$tableName-";
                            errors.ERRORS = data.errors;
                            setErrorsModel(errors);
                        }
                    }
                },
                error: function() 
                {
                    console.log(data);
                } 	        
           });
        }).on('submit', function(e){
            e.preventDefault();
        });
        
    });
        
    var clearDetailForm = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idcitizen':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
JS;
$this->registerJs($script);

$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#details'});
    };
    
    var deleteDetail = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Eliminar este Registro?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            var params = {};
            var data = {'id':id};
            params.URL = "$urlDetail/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal("Registro Eliminado", data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
        
    
JS;
$this->registerJs($script, $this::POS_HEAD);

?>