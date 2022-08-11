<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Type;
use common\models\State;
use yii\helpers\ArrayHelper;
use backend\models\Servicecentreservice;
/* @var $this yii\web\View */
/* @var $model \common\models\Servicecentres */
/* @var $searchModel \common\models\Servicecentres */

frontend\assets\AppAsset::register($this);

$this->title = 'Monitoreo de Servicios';
$this->params['breadcrumbs'][] = $this->title;

$filterType = $model->getTypes();
$filterState = $model->getStates();
$refreshTime = 30000;

$url = Yii::$app->getUrlManager()->createUrl('monitoring');
$states = [
    Servicecentreservice::STATE_ACTIVE,
    Servicecentreservice::STATE_ERROR,
    Servicecentreservice::STATE_WARNING,
];
$showInactive = false;
$showInactive ? array_push($states, Servicecentreservice::STATE_INACTIVE) : null;
?>
<div class="monitoring-index">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-12">
                    <span class="pull-left"><label class="small-info-box-icon" style="display: none" id="lbl_updatemessage"><i class='fas fa-refresh'></i> Actualizando Datos...</label></span>
                    <span class="pull-right">&Uacute;ltima Actualizaci&oacute;n: <label class="small-info-box-icon" id="lbl_timelastupdate"></label></span>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row-fluid">
                <?php
                    foreach ($searchModel as $serv){
                        echo "<div class='col-xs-2'>"
                        . "<div class='card servicecentre' id='$serv->Id'>"
                                . "<div class='card-body'>"
                                . "<h5 class='card-title'>"
                                . "<button class='btn btn-box-tool btn-refresh'><i class='fas fa-sync'></i></button>"
                                . "$serv->ServiceName"
                                . "</h5>"
                                . "<div class='row'>";
                                foreach($serv->services as $service){
                                    echo in_array($service->state->Code, $states) ? "<div class='col-xs-3 ". str_replace(' ', '_', $service->Code)."'>"
                                    . "<p class='text-center tiny'>"
                                            . "<i class='fas fa-circle fa-2x status-default' alt='$service->Name' title='$service->Name'></i>"
                                            . "$service->Code</p>"
                                    . "</div>" : '';
                                }
                                echo  "</div>"
                                . "</div>"
                        . "</div>"
                        . "</div>";
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
    $(document).ready(function(){
        $("#btn-refresh").on('click', function(){
            getData();
        });
        
        $(".btn-refresh").on('click', function(){
            var p = $(this).parents('div.servicecentre');
            getStatus(p.attr('id'));
        });
    });
JS;
$this->registerJs($js);

$jsHead = <<< JS
    
    var refreshLastUpdate = function(){
        var lastupdate = moment().format('DD-MM-YYYY HH:mm:ss');
        $('#lbl_timelastupdate').html(lastupdate);
    };    
    
    var setValues = function(values){
        $.each(values, function(i, services){
            var isOk = true;
            var cS = 0;
            $.each(services, function(j, tasks){
                var _j = j.replace(' ','_');
                var s = $('#'+i).find('div.'+_j);
                $('#'+i).removeClass('bg-red');
                $.each(tasks, function(k, values){
                    if(jQuery.type(values) === 'object'){
                        if(values.length === 0 || values.state !== 'up') {
                            isOk = false;
                            cS++;
                            s.find('i').removeClass('status-ok')
                                .removeClass('status-warning').removeClass('status-default')
                                .addClass('status-error')
                                .attr('title',j+':'+(values.length !== 0 ? values['addr']+': '+values['state']:k));
                            s.removeClass('bg-red').removeClass('bg-orange');
                        } else {
                            s.find('i').removeClass('status-ok').removeClass('status-default').removeClass('status-error').removeClass('status-warning');
                            s.find('i').addClass('status-'+values['state']).attr('title',k+': '+values['addr']+': '+values['state']);
                            s.removeClass('bg-red').removeClass('bg-orange');
                        }
                    }
                });
            });
            !(isOk) ? $('#'+i).addClass('bg-'+(cS < 2 ? 'orange':'red')): $('#'+i).removeClass('bg-red').removeClass('bg-orange');
        });
    };     
    
    var getStatus = function(id){
        clearInterval(refreshData);
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            setValues(data.values);
            refreshData = setInterval(getData, $refreshTime);
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var getData = function(id){
        var params = {};
        var data = {};
        params.URL = "$url/getdata/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.BEFORESEND = function(){
            $("#lbl_updatemessage").show();
        };
        params.SUCCESS = function(data){
            $("#lbl_updatemessage").hide();
            setValues(data.values);
            refreshLastUpdate();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    refreshData = setInterval(getData, $refreshTime);
    setTimeout(getData, 1000);
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>
