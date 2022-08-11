<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Profile */

$this->title = 'Crear Perfil';
$this->params['breadcrumbs'][] = ['label' => 'Perfiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<div class="profile-create">

    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin([
            'id'=>$formName,
        ]); ?>
        <?= $this->render('_form', [
            'model' => $model, 'form'=>$form
        ]) ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <span class="float-right">
                        <?= Html::submitButton('Crear', ['class' =>'btn btn-success']) ?>
                        <?= Html::a('Cancelar', ['index'] ,['class' => 'btn btn-danger']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
