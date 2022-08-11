<?php
use backend\components\CustomMenu;
use yii\helpers\Url;

$menuItems = [];
$submenu = [];
if (!empty(Yii::$app->session->get('itemsMenu'))) {
    $menuItems = array_merge($menuItems, Yii::$app->session->get('itemsMenu'));
}

if(!empty(Yii::$app->session->get('subMenu'))){
    $submenu = Yii::$app->session->get('subMenu');
}
$_controller = Yii::$app->controller->id;

if(isset($submenu[$_controller])){
    $_controller = $submenu[$_controller];
}
$itemActive = "@web/" . $_controller;
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=\yii\helpers\Url::home()?>" class="brand-link">
        <img src="<?=  Url::to("@web/img/icon-sm.png?v=1");?>" type="img/png" alt="AppBase" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light"><?=Yii::$app->params['company']['name']?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user card (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <?=CustomMenu::widget([
                'items' => $menuItems,
                'route' => $itemActive,
            ])
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>