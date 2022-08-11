<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Servicecentres;
use common\models\State;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
#use kartik\date\DatePicker;
#use kartik\time\TimePicker;
#use yii\jui\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enviar Correo Confirmación';
$this->params['breadcrumbs'][] = $this->title;

$duisite = empty($searchModel->IdServiceCentre) ? "":"- ".$searchModel->idServiceCentre->Name;
$title = 'Listado de Citas de Ciudadanos '.$duisite." - ".(empty($searchModel->AppointmentDate)  ? date('Y-m-d'):$searchModel->AppointmentDate);
$modelName = StringHelper::basename($searchModel->className());

$url = \Yii::$app->getUrlManager()->createUrl('appointment');
$urlBatch = \Yii::$app->getUrlManager()->createUrl('maintenance/sendbatchmail');
$urlMail = \Yii::$app->getUrlManager()->createUrl('maintenance/sendremindermail');
$filter = Yii::$app->user->can('appointmentFilter');
$export = Yii::$app->user->can('appointmentExport');
$create = Yii::$app->user->can('appointmentCreate');

$appointmentDate = ['attribute'=>'AppointmentDate',
    'filterType'=> GridView::FILTER_DATE,
            'filterWidgetOptions'=> [
                'language'=>'es',
                'readonly'=>true,
                'disabled'=> !$filter,
                'pluginOptions'=> [
                    'format'=>'dd-mm-yyyy',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ],
            ],
            'format' => 'html'
    ];

$toolbar = ['{toggleData}'];
$content = [
    'content'=>Html::button('<i class="fa fa-envelope"></i> Enviar Correos', ['id'=>'btnSendMail','class'=>'btn btn-success']),
];
$toolbar = array_merge($content, $toolbar);
#$toolbar = $export ? array_merge($toolbar,['{export}']):$toolbar;
#echo Yii::$app->formatter->currencyCode;

?>
<div class="appointments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $gridview = [
        'dataProvider' => $dataProvider,
        'id'=>  $modelName.'-grid',
        #'pjax'=>TRUE,
        'panel'=>['type'=>'primary'],
        'toolbar'=>$toolbar,
        'hover'=>true,
        'striped'=>true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'citizenName',
            ],
            $appointmentDate,
            [
                'attribute'=>'AppointmentHour',
                'format' => 'html',
            ],
            [
                'attribute'=>'ShortCode',
                'content'=>function($model){
                    return $model->ShortCode ? $model->ShortCode:'';
                },
            ],
            [
                'attribute'=>'IdServiceCentre',
                'filter'=> ArrayHelper::map(Servicecentres::find()->where(
                        [
                            'IdState'=>  State::findOne(['KeyWord'=>'Servicecentres','Code'=>'ACT'])->Id,
                            'IdType'=> Type::findOne(['KeyWord'=>'Servicecentres','Code'=>'DUISITE'])->Id,
                        ])->select(['Id','Name'])->all(),'Id','Name'),
                'filterType'=> GridView::FILTER_SELECT2,
                'filterWidgetOptions'=>[
                    'disabled'=>!$filter,
                    'size'=>'lg',
                    'theme'=>  \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'options'=>[
                        'placeholder'=>'',
                    ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
                    ],
                ],
                'content'=>function($model){
                    return $model->IdServiceCentre ? $model->idServiceCentre->Name:"";
                },
                'width'=>'20%',
            ],
            [
                'attribute'=>'IdType',
                'filter'=>  ArrayHelper::map(Type::find()->where(['KeyWord'=>'Process'])->select(['Id','Name'])->all(), 'Id', 'Name'),
                'content'=>function($model){
                    return $model->IdType? $model->idType->Name:"";
                },
            ],
//            [
//                'attribute'=>'IdState',
//                'filter'=>  ArrayHelper::map(State::find()->where(['KeyWord'=>'Appointments'])->select(['Id','Name'])->all(), 'Id', 'Name'),
//                'content'=>function($model){
//                    return $model->IdState? $model->idState->Name:"";
//                },
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {sendremindermail}',
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return $model->view ? "<a href='$url' title='Ver Cita'>"
                                . "<span class='glyphicon glyphicon-eye-open'></span></a>":"";
                    },
                    'sendremindermail'=>function ($url, $model) {
                        return $model->sendremindermail ? "<a href='javascript:sendMail($model->Id);' title='Enviar Recordatorio' style='margin-left:5px'>"
                                . "<span class='glyphicon glyphicon-envelope'></span></a>":"";
                                /*"<a href='$url' title='Enviar Recordatorio Cita' style='margin-left:5px'>"
                                . "<span class='glyphicon glyphicon-envelope'></span></a>":"";*/
                    },
                ],
            ],
        ],
    ]; 
    $gridview = $filter ? array_merge($gridview, ['filterModel' => $searchModel]):$gridview;
    ?>
    <?= GridView::widget($gridview);?>
</div>
<?php 
#$url = Url::to('cancel');

$script = <<< JS
    $(document).ready(function(){
        $("#btnSendMail").on('click', function(){
            swal({
                title: "Confirmación?",
                text: "¿Está seguro que desesa Enviar los Correos de Confirmación de la lista?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#006030",
                confirmButtonText: "Enviar",
                closeOnConfirm: true
            },
            function(){
                sendBatch();
            });
        });
   });
JS;
$this->registerJs($script, $this::POS_READY);
$script = <<< JS
   var refreshGrid = function(){
        $.pjax.reload({container:'#$modelName-grid-pjax'});
    };
    
    var sendBatch = function(){
        var _form = {};
        _form.ID = 'AppointmentsSearch-grid-filters';
        _form.GETBYNAME = true;
        _form.UNBOUNDNAME = true;
        _form.SEPARATORS = ['[',']'];
        var data = getValuesForm(_form);

        var params = {};
        params.URL = '$urlBatch';
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            swal("Confirmación de Envío", data.message, "success");
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var sendMail = function(id){
        var name = $("tr[data-key="+id+"]").find("td[data-col-seq=1]").html();
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Enviar un correo de Confirmación de cita para "+name+"?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#006030",
            confirmButtonText: "Enviar",
            closeOnConfirm: true
        },
        function(){
            window.location = '$urlMail/'+id;
        });
    };

JS;
$this->registerJs($script, $this::POS_HEAD);
?>