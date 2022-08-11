<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\components\CustomMenu;
use backend\models\Settingdetail;
use backend\models\Option;
use kartik\widgets\SwitchInput;

if ($this->theme->layout == \webtoolsnz\AdminLte\Theme::LAYOUT_SIDEBAR_MINI) {

    $menuItems = [];
    $submenu = [];
    ?>
    <aside class="main-sidebar">

        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?=CustomMenu::widget([
                'options' => ['class' => 'sidebar-menu'],
                'items' => $this->theme->mainMenuItems,
            ])
            ?>
        </section>
        <!-- /.sidebar -->
    </aside>

<?php } ?>
