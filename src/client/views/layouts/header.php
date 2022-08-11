<?php

use yii\bootstrap\Nav;
use yii\bootstrap\Html;
use webtoolsnz\AdminLte;
use yii\helpers\Url;

$urlHome = Url::home();

$menuItems = [];

$this->theme->topMenuItems = $menuItems;

?>

<header class="main-header">
    <?php if ($this->theme->layout == AdminLte\Theme::LAYOUT_SIDEBAR_MINI) { ?>
        <!-- Logo -->
        <a href="<?=$urlHome?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?= Html::img('@web/img/logo-alt.png', ['alt' => \Yii::$app->name]) ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?= Html::img('@web/img/smalllogo.png', ['alt' => \Yii::$app->name]) ?></span>
        </a>
    <?php } ?>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => $this->theme->topMenuItems
            ]); ?>
        </div>

    </nav>
</header>