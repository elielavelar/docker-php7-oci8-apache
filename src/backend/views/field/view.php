<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Field */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Campos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fields-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea Eliminar este Registro?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=
                    DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'Id',
                            'Name',
                            'KeyWord',
                            'Code',
                            'IdType',
                            'IdState',
                            'HasCatalog',
                            'Description:ntext',
                            'Value',
                            'MultipleValue',
                        ],
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
</div>
