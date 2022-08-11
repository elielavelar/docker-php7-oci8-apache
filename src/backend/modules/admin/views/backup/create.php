<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\BackupSchema */

$this->title = 'Crear Respaldo';
$this->params['breadcrumbs'][] = ['label' => 'Respaldo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-create">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
