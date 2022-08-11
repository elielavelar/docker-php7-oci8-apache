<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\StringHelper;
use miloschuman\highcharts\Highcharts;
use kartik\date\DatePicker;
use yii\web\JsExpression;

$urlData = \Yii::$app->getUrlManager()->createUrl('reports');
$Y = date('Y');
/* @var $this yii\web\View */
/* @var $centres \common\models\Servicecentres */

$this->title = 'Reporte de Citas por Tipo de Trámite';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Citas por Duicentro';

$urlData = \Yii::$app->getUrlManager()->createUrl('reports');
$Y = date('Y');

$months = array_merge([0=>'Todos'],$months);

$years = [];
$val = $setting->Value;
for($i = $val; $i <= ($Y+1); $i++){
    $years[$i] = $i;
}

$chartType = 'chartType';
$data = [];
foreach ($types as $type){
    $data[] = [$type, 0];
}
$records[] = ["name"=>"Trámites",'colorByPoint'=>TRUE, "data" => $data];
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
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <?=Highcharts::widget([
                        'id'=>$chartType,
                        'scripts' => ['modules/exporting','modules/offline-exporting'],
                        'options'=>[
                            'chart'=>['type'=>'column'],
                            'title'=> ['text'=>$this->title],
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
                            'credits'=> ['text'=> Yii::$app->params['company']['name'],'href'=>'#'],
                        ],
                ]);?>
            </div>
        </div>
    </div>
</div>
<?= Html::hiddenInput('total', 0, ['id'=>'total']);?>
<?php
$script = <<< JS
   
   var getData = function(){
       getDataByType();
   };
    
    var getDataByType = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var data = {'AppointmentDate': AppointmentDate, AppointmentMonth: AppointmentMonth};
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
                    text: '$this->title '+title
                },
                series: dataValues
            });
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
        
    var getDataByCentre = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var data = {'AppointmentDate': AppointmentDate};
        var params = {};
        params.URL = "$urlData/getdatabycentre";
        params.DATA = {'data':JSON.stringify(data)};
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.SUCCESS = function(data){
            var values = data.data;
            var dataValues = values.dataset;
            var drillDown = values.drilldown;
            var chart = $("#").highcharts();
            chart.update({
                title: '$this->title '+ AppointmentDate,
                series: {data: dataValues}
                ,drilldown: drillDown
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
   });
JS;
$this->registerJs($script_ready, $this::POS_READY);
?>