<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\web\View;

use backend\models\Securityincident;
use common\models\State;
use common\models\Type;
use backend\models\Incidentcategory;
use common\models\Servicecentres;
use backend\models\Incident;
use backend\models\SecurityincidentSearch;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SecurityincidentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Incidentes de Seguridad';
$this->params['breadcrumbs'][] = $this->title;

$tableName = StringHelper::basename(Securityincident::tableName());
$formIndex = strtolower(StringHelper::basename(SecurityincidentSearch::class));
$create = Yii::$app->customFunctions->userCan($tableName.'Create');
$update = Yii::$app->customFunctions->userCan($tableName.'Update');
$delete = Yii::$app->customFunctions->userCan($tableName.'Delete');
$view = Yii::$app->customFunctions->userCan($tableName.'View');

$template = "";
$template .= $view ? '{view} ': '';
$template .= $update ? '{update} ': '';
$template .= $delete ? '{delete} ': '';
$date = date('Y-m-d_H_i_s');
$title = $this->title."_".$date;
$formName = 'search_form';

$filterState = ArrayHelper::map(State::findAll(['KeyWord'=> StringHelper::basename(Securityincident::class)]), 'Id', 'Name');
$filterType = ArrayHelper::map(Type::findAll(['KeyWord' => StringHelper::basename(Securityincident::class)]),'Id','Name');
$filterRisk = ArrayHelper::map(Type::find()
        ->joinWith('state b')
        ->where([
            'b.KeyWord' => StringHelper::basename(Type::class),
            'b.Code' => Type::STATUS_ACTIVE,
            'type.KeyWord' => StringHelper::basename(Securityincident::class)."Level"
        ])
        ->orderBy(['type.Value'=> SORT_ASC])
        ->all()
        , 'Id', 'Name');
$filterPriority = ArrayHelper::map(Type::find()
        ->joinWith('state b')
        ->where([
            'b.KeyWord' => StringHelper::basename(Type::class),
            'b.Code' => Type::STATUS_ACTIVE,
            'type.KeyWord' => StringHelper::basename(Incident::class)."Priority"
        ])
        ->orderBy(['type.Value'=> SORT_ASC])
        ->all()
        , 'Id', 'Name');

$toolbar = ['{toggleData} {export}'];
?>
<div class="securityincident-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?=$this->render('_search', ['model' => $searchModel, 'formName' => $formName]); ?>

    <p>
        <?= Yii::$app->customFunctions->userCan($tableName.'Create') ? Html::a('<i class="fas fa-plus-circle"></i> Crear Incidencia', ['create'], ['class' => 'btn btn-success']) : ""?>
    </p>

    
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel'=>['type'=>'default'],
        'toolbar'=>false,
        #'toggleDataOptions'=> ['type'=>'all'],
        /*'export'=> [
            #'label'=>'Exportar Lista',
            'showConfirmAlert'=>false,
            'target'=>  GridView::TARGET_SELF,
        ],
        'exportConfig'=> [
            GridView::PDF => [
                'label'=>'PDF',
                'icon' => 'fas fa-file-pdf', 
                'filename'=>  $title,
                'config'=>[
                    'format'=> \kartik\mpdf\Pdf::FORMAT_LEGAL,
                    'orientation'=> \kartik\mpdf\Pdf::ORIENT_LANDSCAPE,
                    'marginLeft'=>5,
                    'marginRight'=>5,
                    'defaultFontSize'=>9,
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
            GridView::CSV => [
                'label' => 'CSV',
                'icon' => 'fas fa-file-csv', 
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => $title.".csv",
                'alertMsg' => 'Archivo CSV Generado para Descarga',
                'options' => ['title' => 'Archivo separado por comas'],
                'mime' => 'application/csv',
                'config' => [
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                ]
            ],
        ],
         * 
         */
        'columns' => [
            #['class' => 'yii\grid\SerialColumn'],
            #'Id',
            #[ 'class' => '\kartik\grid\CheckboxColumn' ],
            [
                'attribute'=> 'Ticket',
                'content' => function($model){
                    return Html::a($model->Ticket, ['view','id' => $model->Id], []);
                },
                'headerOptions'=> [
                    'style'=> 'width: 10%'
                ],
            ],
            ['attribute'=>'TicketDate',
                'filterType'=> GridView::FILTER_DATE,
                        'filterWidgetOptions'=> [
                            'language'=>'es',
                            'readonly'=>true,
                            'pluginOptions'=> [
                                'format'=>'dd-mm-yyyy',
                                'autoclose'=>true,
                                'todayHighlight' => true,
                            ],
                        ],
                        'format' => 'html',
                        'width'=>'10%',
            ],
            ['attribute'=>'IncidentDate',
                'filterType'=> GridView::FILTER_DATE,
                        'filterWidgetOptions'=> [
                            'language'=>'es',
                            'readonly'=>true,
                            'pluginOptions'=> [
                                'format'=>'dd-mm-yyyy',
                                'autoclose'=>true,
                                'todayHighlight' => true,
                            ],
                        ],
                        'format' => 'html',
                        'width'=>'10%',
            ],
            ['attribute'=>'SolutionDate',
                'filterType'=> GridView::FILTER_DATE,
                        'filterWidgetOptions'=> [
                            'language'=>'es',
                            'readonly'=>true,
                            'pluginOptions'=> [
                                'format'=>'dd-mm-yyyy',
                                'autoclose'=>true,
                                'todayHighlight' => true,
                            ],
                        ],
                        'format' => 'html',
                        'width'=>'10%',
            ],
            [
                'attribute'=> 'IdServiceCentre',
                'filter'=> $model->getServiceCentres(),
                'content'=> function($model){
                    return $model->IdServiceCentre ? $model->serviceCentre->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 10%'
                ],

            ],

            //'InterruptDate',
            //'SolutionDate',
            'Title',
            [
                'attribute' => 'IdState',
                'filter'=> $filterState,
                'content'=> function($model){
                    return $model->IdState ? $model->state->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 8%'
                ],
            ],
            [
                'attribute' => 'IdType',
                'filter'=> $filterType,
                'content'=> function($model){
                    return $model->IdType ? $model->type->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 8%'
                ],
            ],
            [
                'attribute' => 'IdLevelType',
                'filter'=> $filterRisk,
                'content'=> function($model){
                    return $model->IdLevelType ? $model->levelType->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 5%'
                ],
            ],
	    [
                'attribute' => 'IdInterruptType',
                'filter'=> $model->getInterruptTypes(),
                'content'=> function($model){
                    return $model->IdInterruptType ? $model->interruptType->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 5%'
                ],
            ],

            [
                'attribute' => 'IdPriorityType',
                'filter'=> $filterPriority,
                'content'=> function($model){
                    return $model->IdPriorityType ? $model->priorityType->Name:'';
                },
                'headerOptions'=> [
                    'style'=> 'width: 5%'
                ],
            ],
            //'IdIncident',
            //'IdReportUser',
            //'IdType',
            //'IdState',
            //'IdLevelType',
            //'IdInterruptType',
            //'IdUser',
            //'IdCreateUser',
            //'IdGravityType',
            //'IdCategoryType',
            //'Description:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
    
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
        $("#btnReset").on('click', function(){
            $("#$formIndex-year").val('$model->Year');
            $("#$formName").submit();
        });
   });
JS;
$this->registerJs($script, View::POS_READY);
?>