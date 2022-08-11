<?php
use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incident */
/* @var $filterDepartments boolean */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */
/* @var $resourceModel \backend\models\Incidentresource */
/* @var $searchResourceModel \backend\models\search\IncidentresourceSearch */
/* @var $resourceDataProvider \yii\data\ActiveDataProvider */
/* @var $modelTitle \backend\models\Incidenttitle */

$this->title = 'Actualizar Ticket: '.$model->Ticket;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Ticket, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$tableName = $model->tableName();
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app','General'),
                'content' => $this->render('_form', [
                        'model' => $model, 'filterDepartments'=> $filterDepartments,
                        'modelTitle' => $modelTitle,
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
            [
                'label' => Yii::t('app','Resources'),
                'content' => $this->render('_form/_resources',[
                    'modelDetail' => $resourceModel,
                    'searchModel' => $searchResourceModel,
                    'dataProvider' =>  $resourceDataProvider,
                ]),
            ],
        ]]);
    ?>
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
        fetchSubcategories( '$model->IdCategoryType' , '$model->IdSubCategoryType');
        $("#$tableName-idinterrupttype").trigger('change');
   });
JS;
$this->registerJs($script);
