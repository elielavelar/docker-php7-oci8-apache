<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model \backend\models\Setting */
/* @var $modelDetail backend\models\Settingdetail */
/* @var $searchModel \common\models\search\SettingdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Actualizar Parámetro: ' . $model->Name;
$this->params['breadcrumbs'][] = 'Configuración';
$this->params['breadcrumbs'][] = ['label' => 'Parámetros', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => true
                ],
                [
                    'label' => 'Detalles',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        ]),
                    'active' => false
                ],
            ]
        ]);
    ?>
</div>