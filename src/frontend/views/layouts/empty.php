<?php
use kartik\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700');
$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
\hail812\adminlte3\assets\PluginAsset::register($this)->add(['fontawesome', 'icheck-bootstrap']);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?=Yii::$app->params['company']['name']?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition">
    <?php  $this->beginBody() ?>
    <div class="content">
        <div class="card">
            <div class="card-header">
                <a href="<?=Yii::$app->homeUrl?>"><?= Html::img('@web/'.Yii::$app->params['company']['logo-min'], ['alt' => \Yii::$app->name, ]) ?></a>
            </div>
            <!-- /.login-logo -->
            <div class="card-body">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- /.login-box -->

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
