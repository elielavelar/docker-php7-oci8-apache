<?php

use kartik\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\StringHelper;
use kartik\date\DatePicker;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;

$urlData = \Yii::$app->getUrlManager()->createUrl('reports');
$Y = date('Y');
/* @var $this yii\web\View */
/* @var $setting backend\models\Settingsdetail */
/* @var $centres \common\models\Servicecentres */

$this->title = 'Consolidado de Reportes Sistema Citas';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Consolidado Reportes';

$months = array_merge([0=>'Todos'],$months);

$years = [];
$val = $setting->Value;
for($i = $val; $i <= ($Y+1); $i++){
    $years[$i] = $i;
}

$monthNames = \Yii::$app->customFunctions->getMonthNames();
$chartSignUp = "signup-citizen";
$chartAppointment = "appointment-month";
$chartSignUpTitle = 'Ciudadanos Registrados';
$chartAppointmentTitle = 'Citas Registradas';
$monthsData = [0,0,0,0,0,0,0,0,0,0,0,0];
$recordsSignUp = [];
$recordsSignUp[0] = ['name'=>'Aplicación en Línea','data'=>$monthsData];
$recordsSignUp[1] = ['name'=>'Call Center','data'=>$monthsData];


$chartServiceCentre = "appointment-service";
$centretitle = 'Citas de Ciudadanos por Duicentro';

$datacentres = [];
$drilldown = [];

$dtypes = [];
foreach ($types as $t){
    #$dtypes[] = [$t["Name"] => 0];
    $dtypes[] = [$t["Name"],0];
}

foreach ($centres as $c){
    $datacentres[] = ["name"=>$c,"y"=>0,"drilldown"=>$c];
    $drilldown[] = ["id"=>$c, "data"=>$dtypes];
}

$typetitle = 'Citas por Tipo de Trámite';
$chartType = 'chartType';
$data = [];
foreach ($types as $type){
    $data[] = [$type, 0];
}
$records[] = ["name"=>"Trámites",'colorByPoint'=>TRUE, "data" => $data];

?>
<h2><?=$this->title?></h2>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-2">
                <?= Html::label('Año', 'AppointmentDate');?>
                <?= Html::dropDownList('AppointmentDate', $Y, $years, ['id'=>'AppointmentDate','class'=>'form-control'])?>
            </div>
            <div class="col-2">
                <?= Html::label('Mes', 'AppointmentMonth');?>
                <?= Html::dropDownList('AppointmentMonth', 0, $months, ['id'=>'AppointmentMonth','class'=>'form-control'])?>
            </div>
            <div class="col-4">
                <label>MESES ANTERIORES</label>
                <div class="row">
                    <div class="col-6">
                        <span>
                            <?= Html::checkbox('showBeforeMonth', $model->showBeforeMonth, ['id'=>'showBeforeMonth','class'=>''])?>
                            <?= Html::label('Mostrar', 'showBeforeMonth');?>
                        </span>
                    </div>
                    <div class="col-6">
                        <span>
                            <?= Html::checkbox('includeBeforeMonth', $model->includeBeforeMonth, ['id'=>'includeBeforeMonth','class'=>''])?>
                            <?= Html::label('Sumar en Totales', 'includeBeforeMonth');?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <br/>
                <span>
                    <?= Html::checkbox('includeCitizenWithoutApp', $model->includeCitizenWithoutApp, ['id'=>'includeCitizenWithoutApp','class'=>''])?>
                    <?= Html::label('Ciudadanos sin Cita', 'includeCitizenWithoutApp');?>
                </span>
            </div>
            <div class="col-2">
                <label style="display: block">.</label>
                <span class="float-right">
                    <button type="button" id="btnExport" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Exportar
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?=Highcharts::widget([
                                'id'=>$chartSignUp,
                                'scripts'=>['modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>['type'=>'column'],
                                    'title'=> ['text'=>$chartSignUpTitle],
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
                                    'series' => $recordsSignUp,
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 15, 'x'=> -40],
                                    ],
                                ],
                        ]);?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
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
                                    'series' => $recordsSignUp,
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 15, 'x'=> -40],
                                    ],
                                ],
                        ]);?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?= Highcharts::widget([
                            'id'=> $chartServiceCentre,
                            'scripts' => ['modules/data','modules/drilldown','modules/exporting','modules/offline-exporting'],
                            'options'=>[
                                'chart'=>[
                                    'type'=>'column',
                                    'height'=> 600,
                                ],
                                'title'=>['text'=>'Citas de Ciudadanos por Duicentro'],
                                'subtitle'=>['text'=>'Click en la columna para ver detalles de Trámites'],
                                'xAxis'=>[
                                    #'categories' => $centres,
                                    'type'=>'category',
                                    'labels' => [
                                        'rotation'=> -45,
                                        'style'=>  [
                                            'fontSize' => '10px',
                                            #'fontFamily'=> 'Verdana, sans-serif'
                                        ]
                                    ],
                                ],
                                'yAxis'=>[
                                    'title' => ['text' => 'Citas Ciudadanos'],
                                    'allowDecimals'=> FALSE,
                                ],
                                #'legend'=>['enabled'=>FALSE],
                                'tooltip'=>  [
                                    'headerFormat'=> '<span style="font-size:11px">{series.name}</span><br>',
                                    'pointFormat'=> '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
                                ],
                                'plotOptions'=> [
                                    'series'=> [
                                        'borderWidth'=> 0,
                                        'dataLabels'=> [
                                            'enabled'=> true,
                                            'format'=> '{point.y:.0f}'
                                        ]
                                    ]
                                ],
                                'series'=>[
                                    [
                                        'name'=> 'Citas',
                                        'colorByPoint'=>TRUE,
                                        'data'=> $datacentres,
                                    ]

                                ],
                                'colors'=> $colors,
                                'drilldown'=> [
                                    'series'=> $drilldown,
                                ],
                                'credits'=> [
                                    'text'=> Yii::$app->params['company']['name'],
                                    'href'=>'#',
                                    'position'=>['verticalAlign'=>'top','y'=> 15, 'x'=> -40],
                                ],
                            ],
                        ]);
                    ?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?=Highcharts::widget([
                                'id'=>$chartType,
                                'scripts' => ['modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>['type'=>'column','height'=> 500,],
                                    'title'=> ['text'=>$typetitle],
                                    'xAxis' => [
                                        #'categories' =>  $types,
                                        'type'=> 'category'
                                    ],
                                    'yAxis' => [
                                        "title" => ['text'=> 'Citas Registradas'],
                                        'allowDecimals'=> FALSE,
                                    ],
                                    'plotOptions'=> [
                                        'series'=> [
                                            #'stacking'=> 'normal',
                                            'borderWidth'=> 0,
                                            'dataLabels'=> [
                                                'enabled'=> true,
                                                #'format'=> '{point.y:.0f}',
                                                'formatter'=> new JsExpression('function(){'
                                                        . 'var value = this.y;'
                                                        . 'var label = value+"";'
                                                        . 'var total = parseInt($("#total").val());'
                                                        . 'var perct = total > 0 ? parseInt(value)/total:0;'
                                                        . 'var v = parseFloat(perct)*100;'
                                                        . 'label = label +" / <b>"+v.toFixed(1)+"%</b>";'
                                                        . 'return label;'
                                                        . '}'),
                                            ],
                                        ]
                                    ],
                                    'colors'=> $colors,
                                    'legend'=>['enabled'=>FALSE],
                                    'series' => $records,
                                    'credits'=> ['text'=> Yii::$app->params['company']['name'],'href'=>'#'
                                        ,'position'=>['verticalAlign'=>'top','y'=> 15, 'x'=> -40],
                                        ],
                                ],
                        ]);?>
                    </div>
                </div>
                <?= Html::hiddenInput('total', 0, ['id'=>'total']);?>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
        
    var getData = function(){
        getDataByMonth();
        getDataByCentre();
        getDataByType();
        getAppsByMonth();
    };
    
    var getDataByMonth = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var showBeforeMonth = $("#showBeforeMonth").is(":checked") ? 1:0;
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var includeCitizenWithoutApp = $("#includeCitizenWithoutApp").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth
            , 'showBeforeMonth':showBeforeMonth
            , 'includeBeforeMonth':includeBeforeMonth
            , 'includeCitizenWithoutApp':includeCitizenWithoutApp};
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
                title: {text: '$chartSignUpTitle de '+ AppointmentDate}
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
        var showBeforeMonth = $("#showBeforeMonth").is(":checked") ? 1:0;
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var includeCitizenWithoutApp = $("#includeCitizenWithoutApp").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth
            , 'showBeforeMonth':showBeforeMonth
            , 'includeBeforeMonth':includeBeforeMonth
            , 'includeCitizenWithoutApp':includeCitizenWithoutApp};
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
        
    var getDataByCentre = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var showBeforeMonth = $("#showBeforeMonth").is(":checked") ? 1:0;
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var includeCitizenWithoutApp = $("#includeCitizenWithoutApp").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth
            , 'showBeforeMonth':showBeforeMonth
            , 'includeBeforeMonth':includeBeforeMonth
            , 'includeCitizenWithoutApp':includeCitizenWithoutApp};
        var title = (parseInt(AppointmentMonth) > 0 ? ' de '+month:'')+' '+AppointmentDate;
        var params = {};
        params.URL = "$urlData/getdatabycentre";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            var values = data.data;
            var dataValues = values.dataset;
            var drillDown = values.drilldown;
            var _series = drillDown.series;
            var chart = $("#$chartServiceCentre").highcharts();
            chart.update({
                series: {data: dataValues},
                title: {text: '$centretitle '+ title}
            });
            chart.options.drilldown.series = _series;
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
        
    var getDataByType = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var showBeforeMonth = $("#showBeforeMonth").is(":checked") ? 1:0;
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var includeCitizenWithoutApp = $("#includeCitizenWithoutApp").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth
            , 'showBeforeMonth':showBeforeMonth
            , 'includeBeforeMonth':includeBeforeMonth
            , 'includeCitizenWithoutApp':includeCitizenWithoutApp};
        var title = (parseInt(AppointmentMonth) > 0 ? ' de '+month:'')+' '+AppointmentDate;
        var params = {};
        params.URL = "$urlData/getappointmentsbytype";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            var dataValues = data.data;
            $("#total").val(dataValues[0].dataSum);
            var chart = $("#$chartType").highcharts();
            chart.update({
                title: {
                    text: '$typetitle '+title
                },
                series: dataValues
            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
      
    var exportSummary = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var showBeforeMonth = $("#showBeforeMonth").is(":checked") ? 1:0;
        var includeBeforeMonth = $("#includeBeforeMonth").is(":checked") ? 1:0;
        var includeCitizenWithoutApp = $("#includeCitizenWithoutApp").is(":checked") ? 1:0;
        var data = {'AppointmentDate': AppointmentDate, 'AppointmentMonth':AppointmentMonth
            , 'showBeforeMonth':showBeforeMonth
            , 'includeBeforeMonth':includeBeforeMonth
            , 'includeCitizenWithoutApp':includeCitizenWithoutApp};
        var title = (parseInt(AppointmentMonth) > 0 ? ' de '+month:'')+' '+AppointmentDate;
        var params = {};
        params.URL = "$urlData/exportsummary";
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
        
        $("#AppointmentDate, #AppointmentMonth").on('change', function(){
            getData();
        }); 
        
        $("#includeBeforeMonth, #showBeforeMonth, #includeCitizenWithoutApp").on('click', function(){
            getData();
        }); 
        
        $("#btnExport").on('click', function(){
            exportSummary();
        });
   });
JS;
$this->registerJs($script_ready, $this::POS_READY);
?>