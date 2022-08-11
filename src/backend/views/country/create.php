<?php

use yii\bootstrap4\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Countries */

$this->title = 'Agregar País';
$this->params['breadcrumbs'][] = ['label' => 'Países', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="countries-create">

    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $this->render('_form', [
            'model' => $model,'form'=>$form
        ]) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
