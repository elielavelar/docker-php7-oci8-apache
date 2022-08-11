<?php

use buttflattery\formwizard\FormWizard;
use common\customassets\helpers\Html;
use backend\models\Incidentrequest;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequest */
/* @var $modelTitle backend\models\Incidenttitle */
/* @var $filterDepartments boolean */

$this->title = Yii::t('app', '{action} {entity}', [
        'action' => Yii::t('app', 'Add'),
        'entity' => Yii::t('system', 'Service Request'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Service Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$idWizard = $tableName.'-wizard';
?>
<div class="incidentrequest-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <div class="card-body">
            <div class="col-12">
                <?= $this->render('_form', [
                        'model' => $model
                    , 'filterDepartments' => $filterDepartments
                    , 'modelTitle' => $modelTitle,
                ])?>
            </div>
        </div>
    </div>
</div>
