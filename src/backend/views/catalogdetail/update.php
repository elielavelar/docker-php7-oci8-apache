<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetail */
/* @var $modelDetail \common\models\Catalogdetailvalue */

$this->title = 'Actualizar Detalle: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Catalogos', 'url' => ['catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalogVersion->catalog->Name, 'url' => ['catalog/update','id'=>$model->catalogVersion->IdCatalog]];
$this->params['breadcrumbs'][] = 'Versiones';
$this->params['breadcrumbs'][] = ['label' => 'Version '.$model->catalogVersion->Version, 'url' => ['catalogversion/update','id'=>$model->IdCatalogVersion]];
$this->params['breadcrumbs'][] = 'Detalles de Catálogo';
$this->params['breadcrumbs'][] = $this->title;
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
                    'label' => 'Valores',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'modelDetail'=>$modelDetail, 
                        ]),
                    'active' => false
                ],
            ]
        ]);
    ?>

</div>
