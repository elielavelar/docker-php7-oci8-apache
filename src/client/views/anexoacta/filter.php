<?php

/* @var $this yii\web\View */
/* @var $model \common\models\prddui\Anexoacta */

$this->title = 'Anexo de Acta Duicentro '.( $model->COD_CTRO_SERV ? $model->ctroServ->DESC_CTRO_SERV : '');
$this->params['breadcrumbs'][] = $this->title;
$formName = 'frm-search';
$url = Yii::$app->getUrlManager()->createUrl('anexoacta');
?>
<div class="anexoacta-index">
    <?=$this->render('_search', ['model' => $model , 'formName' => $formName]); ?>
    <div class="card" style="overflow-y: visible; height: 100%">
        <iframe id="fileviewer" src="" style="height: 700px; width: 100%; overflow: visible"></iframe>
    </div>
</div>
<?php
$script = <<< JS
    $(document).ready(function(){
        $("body").attr('style',"overflow:scroll");
        $("#fileviewer").attr("src",null);
        $("#btn-filter").on('click', function(){
            $("#message").html('Cargando datos...');
            getData();
        });
        $("#btn-reset").on('click', function(){
            $("#fileviewer").attr("src",null);
        });
    });
JS;
$this->registerJs($script, $this::POS_READY);


$headJS = <<< JS
    var getData = function(){
        var data = new FormData(document.getElementById('$formName'));
        var params = {};
        params.URL = '$url/get';
        params.DATA = data;
        params.METHOD = 'POST';
        params.CACHE = false;
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.SUCCESS = function(data){
            $("#message").empty();
            $("#fileviewer").attr("src",data.path).trigger("resize");
            $(document).trigger("resize");
            swal({
                title: data.title,
                text: data.message,
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#00A65A",
                confirmButtonText: "Aceptar",
                closeOnConfirm: true
            }, function(){
                
            });
        };
        params.ERROR = function(data){
            $("#message").empty();
            swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
            if(data.errors){
                var errors = {};
                errors.ID = '$formName';
                errors.ERRORS = data.errors;
                errors.EXTRA = function(){};
                setErrorsModel(errors);
            }
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($headJS, $this::POS_HEAD);
?>