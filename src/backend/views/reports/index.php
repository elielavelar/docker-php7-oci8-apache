<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $models backend\models\Options */

$this->title = "Reportes";
$this->params['breadcrumbs'][] = $this->title;

?>


<h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="maintenance-index">
    <div class="row-fluid">
        <?php 
        foreach ($models as $opt){
            $url = $opt->Url ? Url::to([$opt->Url]):'#';
            echo "<div class='col-3'>"
                . "<a class='btn btn-lg bg-info' href='$url'>"
                . "<i class='$opt->Icon fa-5x'></i> <h5>$opt->Name</h5></a>"
                . "</div>";
        }
        /*foreach ($options as $opt){
            $url = Url::to($opt["url"]);
            echo "<div class='col-3'>"
                . "<a class='btn btn-lg btn-success' href='$url'>"
                . "<i class='$opt[icon] fa-5x'></i> <h5>$opt[name]</h5></a>"
                . "</div>";
        }*/?>
    </div>
</div>
