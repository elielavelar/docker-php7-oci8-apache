<?php
use yii\bootstrap4\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View */
/* @var $content string */

?>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="icon" href="<?=  Url::to("@web/img/favicon.png?v=1");?>" type="img/png">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>