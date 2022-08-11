<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \common\models\Servicecentres */
/* @var $modelDetail backend\models\Servicecentreservices */
$template = '{view} {update} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;{delete}';
$urlDetail = Yii::$app->urlManager->createUrl('servicecentreservice');
$gridNameServices = 'dt-grid-services';
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="float-left">
                    <h4 class="card-title">Configuraci&oacute;n de Servicios</h4>
                </span>
            </div>
        </div>
        <div class="card-body">
            <p>
                <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar Servicio', ['servicecentreservice/create', 'id' => $model->Id], ['class' => 'btn btn-success']) ?>
            </p>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'id'=>$gridNameServices,
                            'pjax' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                #'Id',
                                'Name',
                                [
                                    'attribute' => 'IdType',
                                    'content' => function($model) {
                                        return $model->IdType ? $model->type->Name : '';
                                    },
                                    'filter' => $modelDetail->getTypes(),
                                ],
                                [
                                    'attribute' => 'IdState',
                                    'content' => function($model) {
                                        return $model->IdState ? $model->state->Name : '';
                                    },
                                    'filter' => $modelDetail->getStates(),
                                ],
                                //'Description:ntext',
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => $template,
                                    'buttons' => [
                                        'view' => function ($url, $model) {
                                            $urlDetail = \Yii::$app->getUrlManager()->createUrl(['servicecentreservice/view','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-eye"></span>',$urlDetail, [
                                                        'title' => Yii::t('app', 'Ver Servicio'),
                                            ]);
                                        },
                                        'update' => function ($url, $model) {
                                            $urlDetail = \Yii::$app->getUrlManager()->createUrl(['servicecentreservice/update','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-edit"></span>', $urlDetail, [
                                                        'title' => Yii::t('app', 'Actualizar Servicio'),
                                            ]);
                                        },
                                        'delete' => function ($url, $model) {
                                            #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                                        'title' => Yii::t('app', 'Eliminar Servicio'),
                                            ]);
                                        },
                                    ],
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
        $('.hours').on('click',function(){
            var chk = $(this);
            var checked = chk.is(':checked') ? 1:0;
            var inp = chk.parents('div').parent('div.row-fluid').find('input[type=text]');
            var isreadonly = inp.attr('readonly');
            if(isreadonly){
                inp.removeAttr('readonly');
            } else {
                inp.attr('readonly',true)
                    .val('0');
            }
        });
   });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
$jsHead = <<< JS
    var refreshGridService = function(){
        $.pjax.reload({container:'#$gridNameServices-pjax'});
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
            var data = {'Id':id};
            params.URL = "$urlDetail/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal("Registro Eliminado", data.message, "warning");
                refreshGridService();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
JS;
$this->registerJs($jsHead, yii\web\View::POS_HEAD);
?>