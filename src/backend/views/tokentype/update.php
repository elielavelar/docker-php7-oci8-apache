<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\TokenType */
/* @var $modelDetail backend\models\Typefields */
/* @var $searchModel backend\models\TypefieldSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Actualizar Tipo de Token: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Token', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
$formName = $model->tableName().'-form';
?>
<div class="token-type-update">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model, 'formName' => $formName]),
                        'active' => true
                    ],
                    [
                        'label' => 'Detalles',
                        'content' => $this->render('_form/_detail',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,]),
                    ],
                ]]);
         ?>
    </div>
</div>
