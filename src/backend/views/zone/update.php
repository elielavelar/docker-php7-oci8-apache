<?php

use yii\helpers\Html;
use common\models\Zones;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Zones */
/* @var $modelDetail common\models\Zonesupervisors */
/* @var $searchModel backend\models\ZonesupervisorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelService common\models\Servicecentres */
/* @var $searchService backend\models\ServicecentresSearch */
/* @var $dataProviderService yii\data\ActiveDataProvider */

$this->title = 'Actualizar Zona: '. $model->Name;
$this->params['breadcrumbs'][] = 'Administración';
$this->params['breadcrumbs'][] = 'Catálogos';
$this->params['breadcrumbs'][] = ['label' => 'Zonas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name , 'url' => ['view','id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<div class="zone-create">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model, 'formName' => $formName,]),
                        'active' => true
                    ],
                    [
                        'label' => 'Supervisores',
                        'content' => $this->render('_form/_supervisors',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,]),
                    ],
                    [
                        'label' => 'Duicentros',
                        'content' => $this->render('_form/_servicecentres',['model'=>$model, 'searchModel'=>$searchService, 'dataProvider'=>$dataProviderService, 'modelDetail' => $modelService]),
                    ],
                ]]);
         ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <span class="float-right">
                        <?=Html::button('<i class="fas fa-save"></i> Guardar',['type' => 'button','id' => 'btnSave','class' => 'btn btn-success'])?>
                        <?=Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class' => 'btn btn-danger'])?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
   $(document).ready(function(){
        $('#btnSave').on('click', function(){
            $('#$formName').submit();
        });
   });
JS;
$this->registerJs($js, $this::POS_READY);
?>
