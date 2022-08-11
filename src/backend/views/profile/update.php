<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Profile */

$this->title = 'Actualizar Perfil: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Perfiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$parentName = StringHelper::basename($model->className());
$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<div class="profile-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin([
            'id'=>$formName,
        ]); ?>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model,'form'=>$form]),
                        'active' => true
                    ],
                    [
                        'label' => 'Configuración ',
                        'content' => $this->render('_form/_formDetail',[
                            'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail
                            ]),
                        #'active' => true
                    ],
                ]]);
         ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <div class="float-right">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Actualizar', ['class' =>'btn btn-primary']) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'] ,['class' => 'btn btn-danger']) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
