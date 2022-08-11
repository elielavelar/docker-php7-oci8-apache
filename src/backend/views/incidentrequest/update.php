<?php

use common\customassets\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequest */
/* @var $modelTitle backend\models\Incidenttitle */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */
/* @var $filterDepartments boolean */

$this->title = Yii::t('app', '{action} {entity} : {name}', [
    'action' => Yii::t('app', 'Update'),
    'entity' => Yii::t('system', 'Request'),
    'name' => $model->Code,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Service Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Code, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="incidentrequest-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app','General'),
                    'content' =>  $this->render('_form', [
                        'model' => $model
                        , 'filterDepartments' => $filterDepartments
                        , 'modelTitle' => $modelTitle,
                    ]),
                    'active' => true
                ],
                [
                    'label' => Yii::t('app','Updates'),
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model,
                    ]),
                    'active' => false,
                ],
                [
                    'label' => Yii::t('app','Attachments'),
                    'content' => $this->render('_form/_attachments',[
                        'modelDetail' => $attachmentModel,
                        'searchModel' => $searchAttachmentModel,
                        'dataProvider' =>  $attachmentDataProvider,
                    ]),
                ],

            ]]);
        ?>
    </div>
</div>
