<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->DisplayName;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->getIdentity();
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->customFunctions->userCan('userUpdate') ? Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):""; ?>
        <?= Yii::$app->customFunctions->userCan('userDelete') && $model->Username != $user->Username ? Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea eliminar este registro?',
                'method' => 'post',
            ],
        ]):""; ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'Username',
                            'DisplayName',
                            'Email:email',
                            [
                                'attribute'=>'IdProfile',
                                'value'=>$model->IdProfile ? $model->profile->Name:"",
                            ],
                            [
                                'attribute'=>'IdState',
                                'value'=>$model->IdState ? $model->state->Name:"",
                            ],
                            'CreateDate',
                            'UpdateDate',
                            'PasswordExpirationDate',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>

</div>
