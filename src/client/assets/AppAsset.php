<?php

namespace client\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $sourcePath = '@app/assets/bootstrap3/';
    public $css = [
        #'css/site.css',
        #'css/bootstrapMB.css',
        'css/custom.css',
        'css/docs.min.css',
    ];
    public $js = [
        'js/jquery-migrate-3.0.0.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'omnilight\assets\SweetAlertAsset',
        'frontend\assets\FontAwesomeAsset',
        'frontend\assets\MomentAsset',
        #'frontend\assets\FontFoundationAsset',
        #'frontend\assets\FontelloAsset',
    ];
}
