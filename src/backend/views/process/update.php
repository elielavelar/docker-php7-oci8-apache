<?php
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use kartik\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model backend\models\Process */

$this->title = 'Actualizar Proceso: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Procesos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'General',
                'content' => $this->render('_form', ['model' => $model, 'form' => $form]),
                'active' => true
            ],
            [
                'label' => 'Detalles',
                'content' => $this->render('_form/_detail',['model'=>$model, 'form' => $form,
                    'modelDetail'=>$modelDetail]),
            ],
        ]]);
     ?>
    <div class="card-footer">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger']);?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>