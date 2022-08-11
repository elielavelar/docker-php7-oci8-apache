<?php

use kartik\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\StringHelper;
use kartik\date\DatePicker;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\web\View;

$urlData = \Yii::$app->getUrlManager()->createUrl('securityincidentreport');
$Y = date('Y');
/* @var $this yii\web\View */
/* @var $setting backend\models\Settingsdetail */
/* @var $centres \common\models\Servicecentres */
/* @var $model \backend\models\Securityincident */
$tableName = $model->tableName();
$this->title = 'Consolidado de Reportes Incidencias de Seguridad';
$this->params['breadcrumbs'][] = 'Incidentes de Seguridad';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Consolidado Reportes';

$titleHeader = 'Total Incidencias / Eventos';
$idChartHeader = 'chart-header';

$idChartCategory = 'chart-category';
$titleCategory = 'Total Incidencias por Categoría';

$idChartType = 'chart-type';
$titleType = 'Total Incidencias por Tipo';

$idChartServicecentre = 'chart-servicecentre';
$titleServiceCentre = 'Total Incidencias por Departamento';

$idChartInterrupt = 'chart-interrupt';
$titleInterrupt = 'Total Incidencias por Interrupción';

$idChartMonth = 'chart-month';
$titleMonth = 'Total Incidencias por Mes';

$formName = 'searchForm-'.$tableName;
?>
<h2><?=$this->title?></h2>
<div class="card">
    <div class="card-header">
        <?=$this->render('_form/_search', ['model' => $model, 'formName' => $formName,]); ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?=Highcharts::widget([
                            'id' => $idChartHeader,
                            'scripts'=>['modules/exporting','modules/offline-exporting'],
                            'options'=>[
                                'chart'=>[
                                    'plotBackgroundColor' => null,
                                    'plotBorderWidth' => 0,
                                    'plotShadow' => false,
                                    'height' => '70%',
                                ],
                                'title' => [
                                    'text' => $titleHeader,
                                    'align' => 'left',
                                    'verticalAlign' => 'top',
                                    'y' => 10,
                                    'style' => [
                                        'fontSize' => '10pt'
                                    ],
                                ],
                                'tooltip' => [
                                    'pointFormat' => '{point.title}: <b>{point.percentage:.1f}%</b><br/>Cant: {point.y}'
                                ],
                                'plotOptions' => [
                                    'pie' => [
                                        'dataLabels' => [
                                            'enabled' => true,
                                            'distance' => 5,
                                            'format' => '<b>{point.name}</b>: {point.percentage:.1f} %<br/>Cant: {point.y}',
                                            'style' => [
                                                'fontWeight' => 'bold',
                                                'color' => 'black'
                                            ]
                                        ],
                                        #'startAngle' => -90,
                                        #'endAngle' => 90,
                                        'center' => ['50%', '60%'],
                                        'size' => '80%',
                                    ],
                                ],
                                'series' => [
                                    [ 
                                        'type' => 'pie',
                                        'name' => $titleHeader,
                                        'innerSize' => '40%',
                                        'data' => $sectypes,
                                    ]
                                ],
                                'credits'=> [
                                    'text'=> Yii::$app->params['company']['name'],
                                    'href'=>'#',
                                    'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -10],
                                ],
                            ],
                        ]);?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?= Highcharts::widget([
                            'id'=> $idChartType,
                            'scripts' => ['modules/data','modules/drilldown','modules/exporting','modules/offline-exporting'],
                            'options'=>[
                                'chart'=>[
                                    'type'=>'column',
                                    'height'=> '70%',
                                ],
                                'title'=>['text'=> $titleType],
                                #'subtitle'=>['text'=>'Click en la columna para ver detalles de Trámites'],
                                'xAxis'=>[
                                    #'categories' => $centres,
                                    'type'=>'category',
                                    'labels' => [
                                        'rotation'=> -45,
                                        'style'=>  [
                                            'fontSize' => '8px',
                                            #'fontFamily'=> 'Verdana, sans-serif'
                                        ]
                                    ],
                                ],
                                'yAxis'=>[
                                    'title' => ['text' => $titleType],
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
                                    [],
                                    [],
                                ],
                                #'colors'=> [],
                                'drilldown'=> [
                                    'series'=> [],
                                ],
                                'credits'=> [
                                    'text'=> Yii::$app->params['company']['name'],
                                    'href'=>'#',
                                    'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -10],
                                ],
                            ],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <?= Highcharts::widget([
                                'id'=> $idChartCategory,
                                'scripts' => ['modules/data','modules/drilldown','modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>[
                                        'type'=>'column',
                                        'height'=> '70%',
                                    ],
                                    'title'=>['text'=> $titleCategory],
                                    'subtitle'=>['text'=>'Click en la columna para ver detalles de Trámites'],
                                    'xAxis'=>[
                                        #'categories' => $centres,
                                        'type'=>'category',
                                        'labels' => [
                                            'rotation'=> -45,
                                            'style'=>  [
                                                'fontSize' => '8px',
                                                #'fontFamily'=> 'Verdana, sans-serif'
                                            ]
                                        ],
                                    ],
                                    'yAxis'=>[
                                        'title' => ['text' => $titleCategory],
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
                                            'name'=> 'Incidencias / Eventos',
                                            'colorByPoint'=>TRUE,
                                            'data'=> [],
                                        ]

                                    ],
                                    #'colors'=> [],
                                    'drilldown'=> [
                                        'series'=> [],
                                    ],
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -10],
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
                        <?= Highcharts::widget([
                                'id'=> $idChartInterrupt,
                                'scripts' => ['modules/data','modules/drilldown','modules/exporting','modules/offline-exporting'],
                                'options'=>[
                                    'chart'=>[
                                        'type'=>'column',
                                        'height'=> '70%',
                                    ],
                                    'title'=>['text'=> $titleInterrupt],
                                    'subtitle'=>['text'=>'Click en la columna para ver detalles de Trámites'],
                                    'xAxis'=>[
                                        #'categories' => $centres,
                                        'type'=>'category',
                                        'labels' => [
                                            'rotation'=> -45,
                                            'style'=>  [
                                                'fontSize' => '8px',
                                                #'fontFamily'=> 'Verdana, sans-serif'
                                            ]
                                        ],
                                    ],
                                    'yAxis'=>[
                                        'title' => ['text' => $titleInterrupt],
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
                                            'name'=> 'Incidencias / Eventos',
                                            'colorByPoint'=>TRUE,
                                            'data'=> [],
                                        ]

                                    ],
                                    #'colors'=> [],
                                    'drilldown'=> [
                                        'series'=> [],
                                    ],
                                    'credits'=> [
                                        'text'=> Yii::$app->params['company']['name'],
                                        'href'=>'#',
                                        'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -10],
                                    ],
                                ],
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?= Highcharts::widget([
                            'id'=> $idChartServicecentre,
                            'scripts' => ['modules/data','modules/drilldown','modules/exporting','modules/offline-exporting'],
                            'options'=>[
                                'chart'=>[
                                    'type'=>'column',
                                    'height'=> '70%',
                                ],
                                'title'=>['text'=> $titleServiceCentre],
                                #'subtitle'=>['text'=>'Click en la columna para ver detalles de Trámites'],
                                'xAxis'=>[
                                    'categories' => [],
                                    'crosshair' => true,
                                    #'type'=>'category',
                                    'labels' => [
                                        'rotation'=> -45,
                                        'style'=>  [
                                            'fontSize' => '8px',
                                            #'fontFamily'=> 'Verdana, sans-serif'
                                        ]
                                    ],
                                ],
                                'yAxis'=>[
                                    'title' => ['text' => $titleServiceCentre],
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
                                        'name'=> 'Incidencias',
                                        #'color'=>'#D84817',
                                        'data'=> [],
                                    ],
                                    [
                                        'name'=> 'Eventos',
                                        #'color'=>'#F4CB42',
                                        'data'=> [],
                                    ],

                                ],
                                #'colors'=> $colors,
                                #'drilldown'=> [
                                #    'series'=> [],
                                #],
                                'credits'=> [
                                    'text'=> Yii::$app->params['company']['name'],
                                    'href'=>'#',
                                    'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -10],
                                ],
                            ],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
        getData();
        
        $("#btnFilter").on('click', function(){
            getData();
        });
        
        $("#btnReset").on('click', function(){
            var form = {};
            form.ID = '$formName';
            form.DEFAULTS = {'$tableName-year': '$model->Year'};
            clearForm(form);
            getData();
        });
   });
   
   var getData = function(){
        getIncidentSummary();
        getIncidentByType();
        getIncidentByCategory();
        getIncidentByInterrupt();
        getIncidentByServiceCentre();
        getIncidentByMonth();
   };
    
   var defaultRequest = function(url, success){
       var data = new FormData(document.getElementById('$formName'));
       var params = {};
       params.URL = url;
       params.DATA = data;
       params.DATATYPE = 'json';
       params.METHOD = 'POST';
       params.CACHE = false;
       params.PROCESSDATA = false;
       params.CONTENTTYPE = false;
       params.SUCCESS = success;
       params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);  
   };
    
   var getIncidentSummary = function(){
        var url = '$urlData/gettotalevents';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var dataValues = data.data;
            var chart = $("#$idChartHeader").highcharts();
            chart.update({
                series: {data: dataValues, name : '$titleHeader'},
                title: {text: '$titleHeader '+ (_year.length > 0 ? 'de '+_year:'')}
            });
        };
        defaultRequest(url, success);  
   };
    
   var getIncidentByCategory = function(){
        var url = '$urlData/gettotalbycategory';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var values = data.data;
            var dataValues = values.dataset;
            var categories = values.categories;
            var drillDown = values.drilldown;
            var _series = drillDown.series;
            var chart = $("#$idChartCategory").highcharts();
            chart.update({
                xAxis : {
                    categories: categories
                },
                series: {data: dataValues},
                title: {text: '$titleCategory '+ (_year.length > 0 ? 'de '+_year:'')}
            });
            chart.options.drilldown.series = _series;
        };
        defaultRequest(url, success);  
   };
    
   var getIncidentByInterrupt = function(){
        var url = '$urlData/gettotalbyinterrupt';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var values = data.data;
            var dataValues = values.dataset;
            var categories = values.categories;
            var drillDown = values.drilldown;
            var _series = drillDown.series;
            var chart = $("#$idChartInterrupt").highcharts();
            chart.update({
                xAxis : {
                    categories: categories
                },
                series: {data: dataValues},
                title: {text: '$titleInterrupt '+ (_year.length > 0 ? 'de '+_year:'')}
            });
            chart.options.drilldown.series = _series;
        };
        defaultRequest(url, success);  
   };
    
   var getIncidentByType = function(){
        var url = '$urlData/gettotalbytype';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var values = data.data;
            var dataValues = values.dataset;
            var categories = values.categories;
            //var drillDown = values.drilldown;
            //var _series = drillDown.series;
            var chart = $("#$idChartType").highcharts();
            chart.update({
                xAxis : {
                    categories: categories
                },
                series: dataValues,
                title: {text: '$titleType '+ (_year.length > 0 ? 'de '+_year:'')}
            });
            //chart.options.drilldown.series = _series;
        };
        defaultRequest(url, success);  
   };
        
   var getIncidentByServiceCentre = function(){
        var url = '$urlData/gettotalbyservicecentre';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var values = data.data;
            var dataValues = values.dataset;
            var categories = values.categories;
        
            //var drillDown = values.drilldown;
            //var _series = drillDown.series;
            var chart = $("#$idChartServicecentre").highcharts();
            chart.update({
                xAxis : {
                    categories: categories
                },
                series: dataValues,
                title: {text: '$titleServiceCentre '+ (_year.length > 0 ? 'de '+_year:'')}
            });
            //chart.options.drilldown.series = _series;
        };
        defaultRequest(url, success);  
   };

   var getIncidentByMonth = function(){
        var url = '$urlData/gettotalbymonth';
        var data = new FormData(document.getElementById('$formName'));
        success = function(data){
            var _year = jQuery.trim($("#$tableName-year option:selected").val());
            var values = data.data;
            var dataValues = values.dataset;
            var categories = values.categories;
            var drillDown = values.drilldown;
            var _series = drillDown.series;
            var chart = $("#$idChartMonth").highcharts();
            chart.update({
                xAxis : {
                    categories: categories
                },
                series: {data: dataValues},
                title: {text: '$titleMonth '+ (_year.length > 0 ? 'de '+_year:'')}
            });
            chart.options.drilldown.series = _series;
        };
        defaultRequest(url, success);  
   };
    
JS;
$this->registerJs($script, View::POS_READY);
?>