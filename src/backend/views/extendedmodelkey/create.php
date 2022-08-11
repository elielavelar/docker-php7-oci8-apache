<?php
use yii\bootstrap4\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Extendedmodelkey */

$this->title = 'Agregar Llave Modelo Extendido';
$this->params['breadcrumbs'][] = 'Configuraciones';
$this->params['breadcrumbs'][] = ['label' => 'Modelos Extendidos', 'url' => ['extendedmodel/index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdExtendedModel ? $model->extendedModel->registredModel->Name : ''), 'url' => ['extendedmodel/view' , 'id'=> $model->IdExtendedModel]];
$this->params['breadcrumbs'][] = 'Llaves';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="extendedmodelkeys-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
