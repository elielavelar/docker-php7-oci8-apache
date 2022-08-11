<?php

use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $assetDir string; */
?>
<nav class="main-header navbar navbar-expand navbar-dark navbar-<?=ArrayHelper::getValue(Yii::$app->params, 'system.theme.navbar.color','default')?>">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge navbar-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">0 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <?= Html::a('<i class="fas fa-sync-alt"></i>', ['site/selectcompany'], ['class' => 'nav-link', 'title' => 'Seleccionar Organización']) ?>
        </li>
        <li class="nav-item">
            <?= Html::a('<i class="fas fa-sign-out-alt"></i>', ['site/logout'], ['data-method' => 'post', 'class' => 'nav-link']) ?>
        </li>
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <?=Html::img(Yii::getAlias('@web/img/avatar.png'), ['alt' => 'User', 'class' => 'user-image img-circle']);?>
                <span class="d-none d-md-inline"><?=Yii::$app->getUser()->getIdentity()->DisplayName; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    <?=Html::img(Yii::getAlias('@web/img/avatar.png'), ['alt' => 'User', 'class' => 'elevation-2 img-circle']);?>
                    <p>
                        <?=Yii::$app->getUser()->getIdentity()->DisplayName; ?>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <?= Html::a('Perfil', ['user/profile'], ['class' => 'btn btn-default btn-flat']) ?>
                    <?= Html::a('Cerrar Sesión', ['site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat float-right']) ?>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i
                    class="fas fa-th-large"></i></a>
        </li>
    </ul>
</nav>