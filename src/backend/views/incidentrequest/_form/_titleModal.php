<?php
use yii\bootstrap4\Modal;
use common\customassets\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \backend\models\Incidenttitle */

$tableName = $model->tableName();
$modalName = $tableName.'-modal';
$formName = $tableName.'-form';
$csrfParam = Yii::$app->getRequest()->csrfParam;

$url = Yii::$app->getUrlManager()->createUrl('incidenttitle');
?>
<?php Modal::begin([
    'title' => 'Titulo Incidencia',
    'size' => Modal::SIZE_LARGE,
    'options'=> [
        'id'=> $modalName,
        'tabindex' => false,
    ],
    'footer' => Html::button(Yii::t('app', '{icon} {action}', [
            'icon' => Html::icon('fas fa-save'),
            'action' => Yii::t('app', 'Save'),
        ]), ['class' => 'btn btn-success', 'id' => 'btn-save-title']) . ""
        . Html::button(
            Yii::t('app', '{icon} {action}', [
                'icon' => Html::icon('fas fa-times-circle'),
                'action' => Yii::t('app', 'Cancel'),
            ])
            , [
                'class' => 'btn btn-danger',
                'id' => 'btn-cancel-title',
                'data' => [ 'dismiss' => 'modal']])
]); ?>
<?= $this->render('_titleForm', [
    'model' => $model,
]) ?>
<?php
Modal::end();

$script = <<< JS
   $(document).ready(() => { 
       
       $('#btn-save-title').on('click', (e) => {
           $('#$formName').submit();
       })
       
       $('#$modalName').on('hidden.bs.modal', () => {
            clearTitleModal();
        });
        
       $("#$formName").on('beforeSubmit',function(){
           AjaxHttpRequest({
                url: '$url/save',
                formData: true,
                data: new FormData( document.getElementById('$formName')),
                options: {
                    method: 'POST'
                },
                success: ( data ) => {
                    let response = {}
                    if( data.success ){
                        response = {
                            title: 'Registro Guardado!',
                            text: data.message,
                            icon: 'success',
                            button: 'Aceptar'
                        }
                        $('#$modalName').modal('toggle');
                        fetchIncidentTitles(null, data.Id);
                    } else {
                        response = {
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            button: 'Aceptar'
                        }
                        setErrorsModel({
                            ID: '$formName',
                            PREFIX: '$tableName-',
                            ERRORS: data.errors
                        });
                    }
                    swal(response)
                }
           })
        }).on('submit', (e) => {
            e.preventDefault();
        });
   });
    
    const clearTitleModal = () => {
            let csrf = $('input[name=$csrfParam]').val();
            var frm = {};
            frm.ID = "$formName";
            clearForm(frm);
            $('input[name=$csrfParam]').val(csrf);
        };
JS;
$this->registerJs($script);
