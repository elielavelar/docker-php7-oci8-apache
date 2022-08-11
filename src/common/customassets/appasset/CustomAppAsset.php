<?php

namespace common\customassets\appasset;

class CustomAppAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@common/customassets/appasset/dist/';
    public $basePath = '@web';
    public $js = [
        'js/jquery-migrate-3.0.0.min.js',
        'js/customscripts.js',
        //'js/drilldown.js',
    ];
    public $css = [];
    public $depends = [];
}