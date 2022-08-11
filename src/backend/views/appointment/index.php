<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Servicecentres;
use common\models\State;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentsSearch */
/* @var $availabilityModel common\models\Appointments */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Citas de Ciudadanos';
$this->params['breadcrumbs'][] = $this->title;

$duisite = empty($searchModel->IdServiceCentre) ? "":"- ".$searchModel->serviceCentre->Name;
$title = 'Listado de Citas de Ciudadanos '.$duisite." - ".(empty($searchModel->AppointmentDate)  ? date('Y-m-d'):$searchModel->AppointmentDate);
$modelName = StringHelper::basename($searchModel->className());

$url = \Yii::$app->getUrlManager()->createUrl('appointment');
$filter = Yii::$app->user->can('appointmentFilter');
$filterByDate = Yii::$app->user->can('appointmentFilterByDate');
$filterByServiceCentre = Yii::$app->user->can('appointmentFilterByServiceCentre');
$export = Yii::$app->user->can('appointmentExport');
$create = Yii::$app->user->can('appointmentCreate');

$exportOptions = $export ? [
            'label'=>'Imprimir Lista',
            'showConfirmAlert'=>false,
            'target'=>  GridView::TARGET_SELF,
        ]:FALSE;
$exportConfig = $export ? [
            GridView::PDF => [
                'label'=>'PDF',
                'iconOptions'=>['class'=>'fa fa-print'],
                'filename'=>  $title,
                'config'=>[
                    'format'=> \kartik\mpdf\Pdf::FORMAT_LETTER,
                    'orientation'=> \kartik\mpdf\Pdf::ORIENT_LANDSCAPE,
                    'marginLeft'=>5,
                    'marginRight'=>5,
                    'defaultFontSize'=>10,
                    'methods'=>[
                        'SetHeader'=>[
                            $title
                        ],
                        'SetFooter'=>[
                            ' | | [ Pág. {PAGENO}]',
                        ],
                    ],
                    'options'=>[
                        'title'=>$title,
                    ],
                ],
                
            ],
        ]:[];
$appointmentDate = ['attribute'=>'AppointmentDate',
    'filterType'=> GridView::FILTER_DATE,
            'filterWidgetOptions'=> [
                'language'=>'es',
                'readonly'=>true,
                'disabled'=> !$filterByDate,
                'pluginOptions'=> [
                    'format'=>'dd-mm-yyyy',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ],
            ],
            'format' => 'html',
            'width'=>'15%',
    ];
$status = Yii::$app->user->can('appointmentFilterStatus') ? [
    'attribute'=>'IdState',
    'filter'=>  ArrayHelper::map(State::find()->where(['KeyWord'=>'Appointments'])->select(['Id','Name'])->all(), 'Id', 'Name'),
    'content'=>function($model){
        return $model->IdState? $model->state->Name:"";
    },
    'width'=>'8%',
]:[
    'attribute'=>'IdState',
    'visible'=>false,
];
$toolbar = ['{toggleData}',];
$content = [
    'content'=>Html::a('Registrar', ['create'],['class'=>'btn btn-success']),
];
$create ? $toolbar = array_merge($content, $toolbar):NULL;
$export ? array_push($toolbar,'{export}'):NULL;
#$toolbar = $export ? array_merge($toolbar,['{export}']):$toolbar;
#echo Yii::$app->formatter->currencyCode;
?>
<div class="appointments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo "";#$this->render('_search', ['model' => $availabilityModel]); ?>

    <?php
    $gridview = [
        'dataProvider' => $dataProvider,
        'id'=>  $modelName.'-grid',
        #'pjax'=>TRUE,
        'panel'=>['type'=>'primary'],
        'toolbar'=>$toolbar,
        'toggleDataOptions'=> ['type'=>'all'],
        'export'=> $exportOptions,
        'exportConfig'=> $exportConfig,
        'hover'=>true,
        'striped'=>true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options'=> [
                    'width'=>'3%',
                ],
                'contentOptions'=>[
                    'style'=>'font-size:12px',
                ]
            ],
            [
                'attribute'=>'citizenName',
            ],
            $appointmentDate,
            [
                'attribute'=>'AppointmentHour',
                'format' => 'html',
                'width'=>'10%',
            ],
            [
                'attribute'=>'ShortCode',
                'content'=>function($model){
                    return $model->ShortCode ? $model->ShortCode:'-';
                },
                'width'=> '10%',
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
                    'disabled'=> !$filterByServiceCentre,
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
                    return $model->IdServiceCentre ? $model->serviceCentre->Name:"";
                },
                'width'=>'18%',
                'contentOptions'=>[
                    'style'=>'font-size:12px',
                ]
            ],
            [
                'attribute'=>'IdType',
                'filter'=>  ArrayHelper::map(Type::find()->where(['KeyWord'=>'Process'])->select(['Id','Name'])->all(), 'Id', 'Name'),
                'content'=>function($model){
                    return $model->IdType? $model->type->Name:"";
                },
                'width'=>'11%',
                'contentOptions'=>[
                    'style'=>'font-size:12px',
                ]
            ],
            [
                'attribute'=>'RegistrationMethod',
                'filter'=> ArrayHelper::map(Type::find()->where(['KeyWord'=>'RegistrationMethod'])->select(['Value','Name'])->all(), 'Value', 'Name'),
                'content'=>function($model){
                    return $model->RegistrationMethodName;
                }
            ],
            $status,
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}', // {cancel}
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return $model->view ? "<a href='$url' title='Ver Cita'>"
                                . "<span class='glyphicon glyphicon-eye-open'></span></a>":"";
                    },
                    'update'=>function ($url, $model) {
                        return $model->update ? "<a href='$url' title='Actualizar Cita'>"
                                . "<span class='glyphicon glyphicon-pencil'></span></a>":"";
                    },
                    'delete'=>function ($url, $model) {
                        return $model->delete ? "<a href='$url' title='Eliminar Cita'>"
                                . "<span class='glyphicon glyphicon-trash'></span></a>":"";
                    },
//                    'cancel'=>function ($url, $model) {
//                        return $model->cancel ? "<a href='javascript:cancel($model->Id);' title='Cancelar Cita'>"
//                                . "<span class='glyphicon glyphicon-remove'></span></a>":"";
//                    },
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
    
    var refreshGrid = function(){
        
    };

JS;
$this->registerJs($script, $this::POS_READY);

$script = <<< JS
    
    var cancel = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa cancelar la Cita?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, Cancelar!",
            closeOnConfirm: false
        },
          function(){
            var params = {};
            var data = {'id':id};
            params.URL = "$url/cancel";
            params.DATA = {'data':JSON.stringify(data)},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal({
                        title: "Cancelación de Cita",
                        text: data.message,
                        type: "success"
                },function(){
                    $.pjax.reload({container:'#$modelName-grid-pjax'});
                });
                
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
          });
    };

JS;
$this->registerJs($script, $this::POS_HEAD);
?>