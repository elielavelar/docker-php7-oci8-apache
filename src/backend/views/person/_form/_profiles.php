<?php
use yii\bootstrap4\Html;
use kartik\widgets\SwitchInput;
use common\models\Employee;
/*@var $model common\models\Person */
$employee = new Employee();
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <?=Html::label($model->getAttributeLabel('activeteemployeee'))?>
                <?= SwitchInput::widget([
                    'model' => $model,
                    'attribute' => 'activeemployeee',
                    'options' => [
                        'id' => $model->tableName().'-activeteemployeee',
                    ],
                    'pluginOptions' => [
                        'onText' => 'Sí',
                        'offText' => 'No',
                    ]
                ]);?>
            </div>
            <div class="col-3">
                <?=Html::label($employee->getAttributeLabel('Code'))?>
                <?= Html::label((!empty($model->employee) ? $model->employee->Code : ''),null, ['class' => 'form-control bg-gray-light'])?>
            </div>
        </div>
    </div>
</div>