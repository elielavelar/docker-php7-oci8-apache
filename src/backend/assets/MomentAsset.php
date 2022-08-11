<?php
namespace backend\assets;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
    public $sourcePath = '@bower/moment';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'min/moment.min.js',
        'min/locales.min.js',
    ];
}