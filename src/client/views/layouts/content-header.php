<?php
/**
 * @var $this \yii\web\View
 */

use yii\bootstrap\Html;
use yii\widgets\Breadcrumbs;
use webtoolsnz\AdminLte\widgets\FlashMessages;
use yii\helpers\Url;

?>

<section class="content-header">
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= Breadcrumbs::widget([
        'homeLink' => [
            'label' => 'Inicio',
            'url' => ($this->context ? Url::home() : null),
        ],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>
</section>