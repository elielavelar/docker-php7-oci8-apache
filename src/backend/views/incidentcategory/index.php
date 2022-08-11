<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\models\Incidentcategory;

/* @var $this yii\web\View */
/* @var $model \backend\models\Incidentcategory */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = Yii::t('system', 'Incident Category');
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$dtGrid = $tableName.'-grid';

$controller = Yii::$app->controller->id;
$url =  \Yii::$app->getUrlManager()->createUrl($controller);

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = $view ? '{view}&nbsp;&nbsp;' : '';
$template .= $update ? '{update}&nbsp;&nbsp;': '';
$template .= $delete ? '|&nbsp;&nbsp;{delete}': '';

?>
<div class="options-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= $create ?
                Html::a(
                        '<i class="fas fa-plus-circle"></i> '.Yii::t('app', 'Add').' '. Yii::t('system','Category'),
                        ['create'],
                        ['class'=>'btn btn-success','id'=>'btn-add'])
                : '';?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=GridView::widget([
                            'id' => $dtGrid,
                            'pjax' => true,
                            'dataProvider' => $dataProvider,
                            'hover' => true,
                            'columns' => [

                                [
                                    'label' => $model->getAttributeLabel('IdParent'),
                                    'attribute' =>  Incidentcategory::KEYWORD_PARENT_CATEGORY,
                                    'content' => function($model){
                                        $id = ArrayHelper::getValue($model,'IdParentcategory', 0);
                                        $key = Incidentcategory::KEYWORD_PARENT_CATEGORY;
                                        $buttons = Html::a('<i class="fas fa-plus"></i>', "javascript:addChild($id);",['class' => 'btn btn-success add-group'])
                                            .Html::a('<i class="fas fa-edit"></i>',"javascript:editChild($id);",['class' => 'btn btn-info upd-module'])
                                            .Html::a('<i class="fas fa-trash"></i>',"javascript:deleteChild($id);",['class' => 'btn btn-danger del-module']);;
                                        $buttonGroup = Html::tag('div', $buttons, ['class' => 'btn-group']);
                                        return Html::tag('i', null, ['class' => ArrayHelper::getValue($model, 'IconParentcategory')])
                                            .'&nbsp;&nbsp;<b>'.ArrayHelper::getValue($model, $key ).'</b>'
                                            .'&nbsp;&nbsp;<code style="margin-left: 25px">'. ArrayHelper::getValue($model, 'Code'.$key ).'</code>'
                                            .Html::tag('span', $buttonGroup, ['class' => 'float-right']);
                                    },
                                    'group' => true,
                                    'groupedRow' => true,
                                    'groupOddCssClass' => 'kv-grouped-row',
                                    'groupEvenCssClass' => 'kv-grouped-row',
                                ],
                                [
                                    'label' => $model->getAttributeLabel('IdParent'),
                                    'attribute' => Incidentcategory::KEYWORD_SUBCATEGORY,
                                    'content' => function($model){
                                        $id = ArrayHelper::getValue($model,'IdSubcategory', 0);
                                        if( $id ):
                                            $key = Incidentcategory::KEYWORD_SUBCATEGORY;
                                            $buttons = Html::a('<i class="fas fa-plus"></i>', "javascript:addChild($id);",['class' => 'btn btn-success add-group'])
                                                .Html::a('<i class="fas fa-edit"></i>',"javascript:editChild($id);",['class' => 'btn btn-info upd-module'])
                                                .Html::a('<i class="fas fa-trash"></i>',"javascript:deleteChild($id);",['class' => 'btn btn-danger del-module']);;
                                            $buttonGroup = Html::tag('div', $buttons, ['class' => 'btn-group']);
                                            return Html::tag('i', null, ['class' => ArrayHelper::getValue($model,'IconSubcategory')])
                                                .'&nbsp;&nbsp;<b>'.ArrayHelper::getValue($model, $key ).'</b>'
                                                .'&nbsp;&nbsp;<code style="margin-left: 25px">'.ArrayHelper::getValue($model, 'Code'.$key ).'</code>'
                                                .Html::tag('span', $buttonGroup, ['class' => 'float-right']);
                                        else:
                                            return '';
                                        endif;
                                    },
                                    'group' => true,
                                ],
                                [
                                    'label' => $model->getAttributeLabel('Name'),
                                    'attribute' => 'Name',
                                ],
                                [
                                    'label' => $model->getAttributeLabel('Code'),
                                    'attribute' => 'Code',
                                ],
                                [
                                    'label' => $model->getAttributeLabel('IdState'),
                                    'attribute' => 'State',
                                ],
                                [
                                    'class' => \kartik\grid\ActionColumn::class,
                                    'template' => $template,
                                    'buttons' => [
                                        'view' => function( $url, array $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-eye"></i>'
                                                    , "javascript:viewChild('$id')"
                                                    , []
                                                );
                                        },'update' => function( $url, $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-edit"></i>'
                                                    , "javascript:editChild('$id')"
                                                    , []
                                                );
                                        },
                                        'delete' => function( $url, $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-trash"></i>'
                                                    , "javascript:deleteChild('$id')"
                                                    , [
                                                        //'data' => [
                                                        //    'confirm' => 'Está seguro que desea Eliminar este Registro?',
                                                        //    'method' => 'get',
                                                        //],
                                                    ]
                                                );
                                        },
                                    ]
                                ]
                            ]
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$script = <<< JS
    const addChild = ( id ) => {
        window.location = '$url/create/'+ id;
    };

    const editChild = ( id ) => {
        window.location = '$url/update/'+ id;
    };
    
    const viewChild = ( id ) => {
        window.location = '$url/view/'+ id;
    };
JS;
$this->registerJs( $script, \yii\web\View::POS_HEAD);