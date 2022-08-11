<?php
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentres */

$this->title = 'Crear Parámetro';
$this->params['breadcrumbs'][] = 'Configuración';
$this->params['breadcrumbs'][] = ['label' => 'Parámetros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model, 
        ]) ?>
    </div>
</div>
