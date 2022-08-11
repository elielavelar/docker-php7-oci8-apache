<?php
use common\customassets\Timeline\Timeline;
use common\customassets\Timeline\TimelineItem;
use backend\models\Incidentdetail;
use yii\bootstrap4\Html;

/* @var $model \backend\models\Incident */
/* @var $this \yii\web\View */
$details = [];
$max = count( $model->incidentdetails );
$cont = 0;
foreach ($model->incidentdetails as $detail){
    $details[ Yii::$app->getFormatter()->asDate($detail->RecordDate, 'php:d-m-Y')][] =
        Yii::createObject([
            'class' => TimelineItem::class,
            'time' => Yii::$app->getFormatter()->asTime($detail->RecordDate),//strtotime($detail->RecordDate),
            'header' => [
                'title' => ( $detail->IdActivityType ? $detail->activityType->Name : ''),
                'buttons' => [
                    [
                        'icon' => 'fas fa-edit',
                        'url' => ['incidentdetail/update', 'id' => $detail->Id],
                        'visible' => (!in_array(
                                ($detail->IdActivityType ? $detail->activityType->Code : null)
                                , [ Incidentdetail::ACTIVITY_ASSIGNMENT, Incidentdetail::ACTIVITY_CLOSE])
                                && ( $cont == ($max - 1) )
                            ),
                    ],
                    [
                        'icon' => 'fas fa-eye',
                        'url' => ['incidentdetail/view', 'id' => $detail->Id],
                    ]
                ]
            ],
            'body' => $this->render('_detailvalues', ['model' => $detail, 'attributes' => $detail->getDescription()]),
            'iconClass' => $detail->getIcon(),
            'iconBg' => $detail->getBgClass(),
        ])
    ;
    $cont++;
}
?>
<div class="card">
    <div class="card-header clearfix">
        <span class="card-title">Actualizaciones</span>
        <?= Html::a('<i class="fas fa-plus-square"></i> Nueva Actualización',['incidentdetail/create','id'=>$model->Id],['class'=>'btn btn-default btn-sm float-right']);?>
    </div>
    <div class="card-body">
        <?=Timeline::widget([
            'sort' => Timeline::SORT_REVERSE,
            'items' => $details
        ])?>
    </div>
</div>

