<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use backend\models\Securityincidentdetails;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincidentdetails */

$controllerName = 'securityincident';
$update = Yii::$app->customFunctions->userCan($controllerName.'Update');
$delete = Yii::$app->customFunctions->userCan($controllerName.'Delete');

$url = $update ? "update/":"";

$this->title = $model->Title;
$this->params['breadcrumbs'][] = ['label' => 'Incidentes de Seguridad', 'url' => ['securityincident/index']];
$this->params['breadcrumbs'][] = ['label' => $model->securityIncident->Ticket, 'url' => ['securityincident/'.$url.$model->IdSecurityIncident]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="securityincidentdetails-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=  $update && ($model->IdActivityType ? $model->activityType->Code != Securityincidentdetails::ACTIVITY_ASSIGNMENT:FALSE) ? Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):'' ?>
        <?= $delete && ($model->IdActivityType ? $model->activityType->Code != Securityincidentdetails::ACTIVITY_ASSIGNMENT:FALSE) ? Html::a('<i class="fas fa-trash-alt"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea Eliminar este Registro?',
                'method' => 'post',
            ],
        ]) : '' ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            #'Id',
                            [
                                'attribute'=>'IdSecurityIncident',
                                'value'=> $model->IdSecurityIncident ? $model->securityIncident->Ticket:"",
                            ],
                            'Title',
                            'Description:ntext',
                            'DetailDate',
                            'RecordDate',
                            'SolutionDate',
                            [
                                'attribute'=>'IdUser',
                                'value'=> $model->IdUser ? $model->user->DisplayName:"",
                            ],
                            [
                                'attribute'=>'IdActivityType',
                                'value'=> $model->IdActivityType ? $model->activityType->Name:'',
                            ],
                            [
                                'attribute'=>'IdAssignedUser',
                                'value'=> $model->IdAssignedUser ? $model->assignedUser->DisplayName: '',
                            ],
                            [
                                'attribute'=>'IdIncidentState',
                                'value'=> $model->IdIncidentState ? $model->incidentState->Name:'',
                            ],
                            'Commentaries:ntext',
                            'Investigation:ntext',
                            'KnowledgeBase:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['securityincident/'.$url.$model->IdSecurityIncident],['class'=>'btn btn-danger'])?>
    

</div>
