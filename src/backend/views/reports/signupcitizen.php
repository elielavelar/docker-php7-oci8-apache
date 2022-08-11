<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\StringHelper;
use kartik\date\DatePicker;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

$urlData = \Yii::$app->getUrlManager()->createUrl('reports');
$Y = date('Y');
/* @var $this yii\web\View */
/* @var $setting backend\models\Settingsdetail */
/* @var $model Object */
/* @var $centres \common\models\Servicecentres */

$this->title = 'Reporte de Registro de Ciudadanos';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registro de Ciudadanos';


$years = [];
$val = $setting->Value;
for($i = $val; $i <= ($Y+1); $i++){
    $years[$i] = $i;
}

$months = array_merge([0=>'Todos'],$months);

$monthNames = \Yii::$app->customFunctions->getMonthNames();
$monthsData = [0,0,0,0,0,0,0,0,0,0,0,0];
$records = [];
$records[0] = ['name'=>'Aplicación en Línea','data'=>$monthsData];
$records[1] = ['name'=>'Call Center','data'=>$monthsData];


$centres[] = $centres;

$chartSignUp = "signup-citizen";
$chartAppointment = "appointment-month";
$chartAppointmentTitle = 'Citas Registradas';
?>
<h2><?=$this->title?></h2>
<div class="row">
    <div class="col-2">
        <?= Html::label('Año', 'AppointmentDate');?>
        <?= Html::dropDownList('AppointmentDate', $Y, $years, ['id'=>'AppointmentDate','class'=>'form-control'])?>
    </div>
    <div class="col-2">
        <?= Html::label('Mes', 'AppointmentMonth');?>
        <?= Html::dropDownList('AppointmentMonth', 0, $months, ['id'=>'AppointmentMonth','class'=>'form-control'])?>
    </div>
    <div class="col-2">
        <?= Html::label('Incluir Meses Anteriores', 'includeBeforeMonth');?>
        <?= Html::checkbox('includeBeforeMonth', $model->includeBeforeMonth, ['id'=>'includeBeforeMonth','class'=>''])?>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <span class="float-right">
                    <?= Html::button('<i class= "fa fa-file-excel-o"></i> Exportar',['id'=>'btnExport','class'=>'btn btn-success','type'=>'button']);?>
                </span>
                <h3>Reporte Registro de Ciudadanos</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?=Highcharts::widget([
                                'id'=>$chartSignUp,
                                'scripts'=>['modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>['type'=>'column'],
                                    'title'=> ['text'=>$this->title],
                                    'xAxis' => [
                                        'categories' =>  $monthNames,
                                    ],
                                    'yAxis' => [
                                        "title" => ['text'=> 'Ciudadanos Registrados'],
                                        "allowDecimals"=> FALSE,
                                    ],
                                    'legend'=>['reserved'=>TRUE],
                                    'plotOptions'=> [
                                        'series'=> [
                                            'stacking'=> 'normal',
                                            'borderWidth'=> 0,
                                            'dataLabels'=> [
                                                'enabled'=> true,
                                                #'format'=> '{point.y:.0f}',
                                                'formatter'=> new JsExpression(' function(){ if(this.y > 0){return this.y;} else {return null;}}'),
                                            ],
                                        ]
                                    ],
                                    'colors'=> $colors,
                                    'series' => $records,
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -100],
                                    ],
                                ],
                        ]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <span class="float-right">
                    <?= Html::button('<i class= "fa fa-file-excel-o"></i> Exportar',['id'=>'btnExportApp','class'=>'btn btn-success','type'=>'button']);?>
                </span>
                <h3>Reporte Citas Ciudadanos Registradas</h3>
                
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?=Highcharts::widget([
                                'id'=>$chartAppointment,
                                'scripts'=>['modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>['type'=>'column'],
                                    'title'=> ['text'=>$chartAppointmentTitle],
                                    'xAxis' => [
                                        'categories' =>  $monthNames,
                                    ],
                                    'yAxis' => [
                                        "title" => ['text'=> 'Citas Registradas'],
                                        "allowDecimals"=> FALSE,
                                    ],
                                    'legend'=>['reserved'=>TRUE],
                                    'plotOptions'=>[
                                        'series'=>[
                                            'stacking'=> 'normal',
                                            'borderWidth'=> 0,
                                            'dataLabels'=> [
                                                'enabled'=> true,
                                                #'format'=> '{point.y:.0f}',
                                                'formatter'=> new JsExpression('function(){ '
                                                        . 'if(this.y > 0){ return this.y; }'
                                                        . 'else { return null; }'
                                                        . '}'),
                                            ],
                                        ],
                                    ],
                                    'colors'=>$colors,
                                    'series' => $records,
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -100],
                                    ],
                                ],
                        ]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    
   var getData = function(){
       getDataByMonth();
       getAppsByMonth();
   };
    
    var getDataByMonth = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth, 'includeBeforeMonth':includeBeforeMonth};
        var params = {};
        params.URL = "$urlData/getdatabymonth";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            var dataValues = data.data;
            var chart = $("#$chartSignUp").highcharts();
            chart.update({
                series: dataValues,
                title: {text: '$this->title de '+ AppointmentDate}
            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
        
    var getAppsByMonth = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth, 'includeBeforeMonth':includeBeforeMonth};
        var params = {};
        params.URL = "$urlData/getappointmentbymonth";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            var dataValues = data.data;
            var chart = $("#$chartAppointment").highcharts();
            chart.update({
                series: dataValues,
                title: {text: '$chartAppointmentTitle de '+ AppointmentDate}
            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
        
    var exportData = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth, 'includeBeforeMonth':includeBeforeMonth};
        var params = {};
        params.URL = "$urlData/exportsignupbymonth";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            swal({
                title: "Reporte Generado!",
                type: "success",
                text: "<span style='display:block'>"+data.message+"</span><br><a href="+data.url+" target='_blank' class='btn btn-primary'><i class='fa fa-download'></i> Descargar</a>",
                html: true,
                showCloseButton: true,
            }, function(){

            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
        
    var exportApp = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth, 'includeBeforeMonth':includeBeforeMonth};
        var params = {};
        params.URL = "$urlData/exportappointmentbymonth";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            swal({
                title: "Reporte Generado!",
                type: "success",
                text: "<span style='display:block'>"+data.message+"</span><br><a href="+data.url+" target='_blank' class='btn btn-primary'><i class='fa fa-download'></i> Descargar</a>",
                html: true,
                showCloseButton: true,
            }, function(){

            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($script, $this::POS_READY);

$script_ready = <<< JS
   $(document).ready(function(){
        getData();
      
        $("#AppointmentDate").on('change', function(){
           getData();
        }); 
        
        $("#AppointmentMonth").on('change', function(){
           getData();
        }); 
        
        $("#includeBeforeMonth").on('click', function(){
           getData();
        }); 
        
        $("#btnExport").on('click', function(){
            exportData();
        });
        
        $("#btnExportApp").on('click', function(){
            exportApp();
        });
   });
JS;
$this->registerJs($script_ready, $this::POS_READY);
?>