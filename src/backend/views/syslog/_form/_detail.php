<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;

use common\models\State;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

#use fedemotta\datatables\DataTables;

/* @var $this yii\web\View */
/* @var $model backend\models\Settings */
/* @var $modelDetail backend\models\Settingsdetail */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\SettingsdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>'Settings']), 'Id', 'Name');
$filterType= ArrayHelper::map(Type::findAll(['KeyWord'=>'Data']), 'Id', 'Name');

$urlDetail = \Yii::$app->getUrlManager()->createUrl('syslogdetail');
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <span class="float-left">
                        <h4 class="card-title">Detalle</h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?php Pjax::begin([
                    'id'=>'details',
                ]); ?>    
                <?= GridView::widget([
                'id'=>'dtgrid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'Attribute',
                    'Value',
                    'OldValue',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons'=>[
                            'view' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "javascript:getDetail($model->Id);", [
                                            'title' => Yii::t('app', 'lead-edit'), 
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
                <h3 class="modal-title" id="Label"><strong>Detalle de Bitácora <div class="inline" id=""></div></strong></h3>
            </div>
            <div class="modal-body">
                <?=$this->render('_detailform', ['model'=>$modelDetail])?>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12">
                        <span class="float-right">
                            <button type="button" id="btnCancelDetail" name="btnCancelDetail" class="btn btn-danger">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    $(document).ready(function(){
        $('#modal-detail').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btnCancelDetail").on('click',function(){
            $("#modal-detail").modal("toggle");
        });
    });
        
    var clearModal = function(){
        var frm = {};
        frm.ID = "settingsdetail-form";
        var defaultvalues = {};
        $.extend(defaultvalues,{'syslogdetail-idsyslog':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
JS;
$this->registerJs($script);

$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#details',async: false});
   };
    
    var getDetail = function(id){
        var params = {};
        var data = {'id':id};
        params.URL = "$urlDetail/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "syslogdetail-form";
            frm.PREFIX = "syslogdetail-";
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#modal-detail").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($script, $this::POS_HEAD);

?>