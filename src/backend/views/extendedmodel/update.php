<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Extendedmodels */

$this->title = 'Actualizar Modelo Extendido: ' . ($model->IdRegistredModel ? $model->registredModel->Name : '');
$this->params['breadcrumbs'][] = 'Configuraciones';
$this->params['breadcrumbs'][] = ['label' => 'Modelos Extendidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdRegistredModel ? $model->registredModel->Name : ''), 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

?>
<div class="extendedmodels-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => TRUE
                ],
                [
                    'label' => 'Llaves',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        ]),
                    #'visible' => $model->IdState ? (in_array($model->state->Code, [Catalogs::STATUS_ACTIVE])):false,
                    'active' => FALSE
                ],
            ]]);
     ?>
    </div>
</div>
