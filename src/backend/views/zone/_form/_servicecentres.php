<?php
use yii\helpers\Html;

use common\models\State;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \common\models\Zones */
/* @var $modelDetail common\models\Servicecentres */
/* @var $searchModel backend\models\ServicecentresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= $modelDetail->getStates();
$filterType = $modelDetail->getTypes();

#$url = \Yii::$app->getUrlManager()->createUrl('zonesupervisor');
$gridName = 'dt-grid-service';

?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <span class="float-left">
                        <h4 class="card-title">Duicentros</h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'id'=>$gridName,
                        'pjax' => true,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            #'Id',
                            'Name',
                            'MBCode',
                            [
                                'attribute'=>'IdState',
                                'filter'=> $filterState,
                                'content'=>function($data){
                                    return $data->IdState ? $data->state->Name:NULL;
                                },
                                'enableSorting'=>TRUE  
                            ],
                            [
                                'attribute'=>'IdType',
                                'filter'=> $filterType,
                                'content'=>function($data){
                                    return $data->IdType ? $data->type->Name:NULL;
                                },
                                'enableSorting'=>TRUE  
                            ],
                        ],
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>