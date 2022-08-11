<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidenttitle */

$this->title = Yii::t('system', 'Actualizar Incidenttitle: ' . $model->Title, [
    'nameAttribute' => '' . $model->Title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incidenttitles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Title, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('system', 'Update');
?>
<div class="incidenttitle-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
