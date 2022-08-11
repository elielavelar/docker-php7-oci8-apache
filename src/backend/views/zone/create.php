<?php

use yii\helpers\Html;
use common\models\Zones;

/* @var $this yii\web\View */
/* @var $model common\models\Zones */

$this->title = 'Agregar Zona';
$this->params['breadcrumbs'][] = 'Administración';
$this->params['breadcrumbs'][] = 'Catálogos';
$this->params['breadcrumbs'][] = ['label' => 'Zonas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<div class="zone-create">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model, 'formName' => $formName
        ]) ?>
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
