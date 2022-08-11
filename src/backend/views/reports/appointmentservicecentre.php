<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\StringHelper;
use kartik\date\DatePicker;
use miloschuman\highcharts\Highcharts;

$urlData = \Yii::$app->getUrlManager()->createUrl('reports');
$Y = date('Y');
/* @var $this yii\web\View */
/* @var $setting backend\models\Settingsdetail */
/* @var $centres \common\models\Servicecentres */

$name = 'Citas por Duicentro';
$this->title = 'Reporte de '.$name;
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $name;

$months = array_merge([0=>'Todos'],$months);

$years = [];
$val = $setting->Value;
for($i = $val; $i <= ($Y+1); $i++){
    $years[$i] = $i;
}
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
$chartServiceCentre = "appointment-service";
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
    <div class="card-header">
        <span class="float-right">
            <button type="button" id="btnExport" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i> Exportar
            </button>
        </span>
        <h3>Reporte de Citas por Duicentro</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
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
                                'position'=>['verticalAlign'=>'top','y'=> 10, 'x'=> -100],
                            ],
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
   var getData = function(){
       getDataByCentre();
   };
    
    var getDataByCentre = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var data = {'AppointmentDate': AppointmentDate, AppointmentMonth: AppointmentMonth};
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
                title: {text: '$this->title '+ title}
            });
            chart.options.drilldown.series = _series;
        };
        params.ERROR = function(data){
            console.log(data);
        };
        AjaxRequest(params);
    };
    
    var exportDataByCentre = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
        var AppointmentMonth = $("#AppointmentMonth option:selected").val();
        var month = $("#AppointmentMonth option:selected").text();
        var data = {'AppointmentDate': AppointmentDate, AppointmentMonth: AppointmentMonth};
        var title = (parseInt(AppointmentMonth) > 0 ? ' de '+month:'')+' '+AppointmentDate;
        var params = {};
        params.URL = "$urlData/exportdatabycentre";
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
        
        $("#btnExport").on('click', function(){
            exportDataByCentre();
        });
   });
JS;
$this->registerJs($script_ready, $this::POS_READY);
?>
