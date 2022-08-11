<?php
use kartik\detail\DetailView;
/* @var $model \backend\models\Incidentrequestdetail */
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'IdIncidentRequest',
                        'value' => $model->IdIncidentRequest ?
                            $model->incidentRequest->Code
                            : ''
                    ],
                    [
                        'attribute' => 'IdActivityType',
                        'value' => $model->IdActivityType ?
                            $model->activityType->Name
                            : ''
                    ],
                    'DetailDate',
                    'RecordDate',
                    [
                        'attribute' => 'IdIncidentRequestState',
                        'value' => $model->IdIncidentRequestState ?
                            $model->incidentRequestState->Name
                            : ''
                    ],
                    [
                        'attribute' => 'IdUser',
                        'value' => $model->IdUser ?
                            $model->user->DisplayName
                            : ''
                    ],
                    [
                        'attribute' => 'IdAssignedUser',
                        'value' => $model->IdAssignedUser ?
                            $model->assignedUser->DisplayName
                            : ''
                    ],
                    'Description:ntext',
                ],
            ]) ?>
        </div>
    </div>
</div>
