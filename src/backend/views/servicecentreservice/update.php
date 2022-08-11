<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentreservice */
/* @var $modelDetail common\models\Servicetask */
/* @var $searchModel common\models\search\ServicetaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Actualizar Service: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Servicio', 'url' => ['servicecentre/index']];
$this->params['breadcrumbs'][] = ['label' => $model->serviceCentre->Name, 'url' => ['servicecentre/view', 'id' => $model->IdServiceCentre]];
$this->params['breadcrumbs'][] = 'Servicios';
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="servicecentreservices-update">
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
                    'label' => 'Configuración ',
                    'content' => $this->render('_form/_detail',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,]),
                    #'visible' => in_array($model->type->Code, [Servicecentres::TYPE_DUISITE,]),
                ],
            ]]);
         ?>
    </div>
</div>
<?php
$js = <<< JS
   $(document).ready(function(){
        
   });
    
JS;
?>