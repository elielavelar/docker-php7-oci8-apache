<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */
use omnilight\assets\SweetAlertAsset;
use webtoolsnz\AdminLte\ThemeAsset;
use frontend\assets\AppAsset;

AppAsset::register($this);
ThemeAsset::register($this);

$this->registerJsFile('@web/js/customscripts.js');

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<?= $this->render('//layouts/head') ?>

<body class="hold-transition <?= $this->theme->customSkin ? $this->theme->customSkin : $this->theme->skin ?> <?= $this->theme->layout ?> <?="sidebar-collapse"?>">
<div class="wrapper">

    <!-- Header Begin -->
    <?= $this->render('//layouts/header') ?>
    <!-- Header Begin -->

    <!-- Sidebar Begin -->
    <?= $this->render('//layouts/sidebar') ?>
    <!-- Sidebar End -->

    <?php $this->beginBody() ?>
    <div class="content-wrapper">
        <!-- Content Header Begin -->
        <?= $this->render('//layouts/content-header') ?>
        <!-- Content Header End -->

        <!-- Content Begin -->
        <?= $this->render('//layouts/content', ['content' => $content]) ?>
        <!-- Content End -->
    </div>
    <?php $this->endBody() ?>

    <!-- Footer Begin -->
    <?= $this->render('//layouts/footer') ?>
    <!-- Footer End -->
</div>
</body>
</html>
<?php $this->endPage() ?>
