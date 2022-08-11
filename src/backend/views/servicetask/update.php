<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Servicetask */
/* @var $modelDetail common\models\Servicetaskcustomstate */
/* @var $SearchModel common\models\search\ServicetaskcustomstateSearch */

$this->title = 'Actualizar Tarea: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Servicio', 'url' => ['servicecentre/index']];
$this->params['breadcrumbs'][] = ['label' => $model->service->serviceCentre->Name, 'url' => ['servicecentre/view','id' => $model->service->IdServiceCentre]];
$this->params['breadcrumbs'][] = 'Servicios';
$this->params['breadcrumbs'][] = ['label' => $model->service->Name, 'url' => ['servicecentreservice/view','id' => $model->IdService]];
$this->params['breadcrumbs'][] = 'Tareas';
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
$tableName = $model->tableName();
$formName = 'form-'.$tableName;
?>
<div class="servicetaskcustomstates-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model,'formName'=>$formName]),
                        'active' => true
                    ],
                    [
                        'label' => 'Estados',
                        'content' => $this->render('_form/_states',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider, 'modelDetail'=>$modelDetail,]),
                        #'visible' => in_array($model->type->Code, [Servicecentres::TYPE_DUISITE,]),
                    ],
                ]]);
         ?>
    </div>
</div>
