<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model frontend\models\Citizen */
/* @var $searchModel common\models\AppointmentsSearch */
/* @var $modelDetail common\models\Appointments */

$this->title = 'Actualizar Datos Ciudadano: ' . $model->CompleteName;
$this->params['breadcrumbs'][] = ['label' => 'Ciudadano', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->CompleteName, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="panel panel-default">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'Datos',
                        'content' => $this->render('_form', ['model' => $model]),
                        'active' => true
                    ],
                    [
                        'label' => 'Citas ',
                        'content' => $this->render('_form/_detail',[
                            'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                            ]),
                        'visible' => $model->IdState ? (in_array($model->state->Code, ['ACT'])):false
                    ],
                ]]);
         ?>
        
    </div>

</div>
