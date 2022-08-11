<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;
use yii\widgets\ActiveForm;
use common\models\Servicecentre;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentre */

$this->title = 'Actualizar Centro de Atención: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Atención', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="servicecentres-update">
    <div class="card">
        <div class="card-header bg-info">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model,'form'=>$form]),
                        'active' => true
                    ],
                ]]);
         ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <span class="float-right">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Actualizar', ['class' =>'btn btn-primary']) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'] ,['class' => 'btn btn-danger']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
