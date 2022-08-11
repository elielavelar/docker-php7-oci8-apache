<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $modelDetail common\models\Personaldocument */
/* @var $searchModel \common\models\search\PersonaldocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Actualizar Persona: ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Personas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="person-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?=Tabs::widget([
            'items' => [
                 [
                     'label' => 'General',
                     'content' => $this->render('_form', [
                         'model' => $model, 'modelDetail' => $modelDetail, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider
                     ]),
                     'active' => true
                 ],
                 [
                     'label' => 'Perfiles',
                     'content' => $this->render('_form/_profiles',[
                         'model'=>$model, 
                         ]),
                     'active' => false
                 ],
                 [
                     'label' => 'Citas',
                     'content' => $this->render('_form/_appointments',[
                         'model'=>$model, 
                         ]),
                     'active' => false
                 ],
             ]     
            ]);
        ?>
    </div>
</div>
<?=$this->render('_form/_modalDetail', ['model'=> $modelDetail])?>