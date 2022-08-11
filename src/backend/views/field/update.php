<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Field */

$this->title = 'Actualizar Campo: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Campos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$view = Yii::$app->customFunctions->userCan($controller.'View');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');

?>
<div class="fields-update">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model, 'modelSource' => $modelSource,]),
                        'active' => true
                    ],
                    [
                        'label' => 'Catálogo',
                        'content' => $this->render('_form/_detail'
                                ,['model'=>$model, 'searchModel'=>$searchModel, 
                                    'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,
                                    'create' => $create, 'update' => $update, 'view' => $view, 
                                    'delete' => $delete, 
                                ]),
                        'visible' => $model->HasCatalog == $model::HAS_CATALOG_TRUE,
                    ],
                ]]);
         ?>
    </div>
</div>
