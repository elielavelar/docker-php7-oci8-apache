<?php

namespace common\customassets\datatables;

use yii\web\AssetBundle;

class DataTablesBoostrapPluginAsset extends AssetBundle
{
    public $sourcePath = '@bower/datatables.net-plugins';
    public $css = [
        "css/dataTables.bootstrap4.min.css",
    ];

    public $js = [
        "js/dataTables.bootstrap4.min.js",
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'sdelfi\datatables\DataTablesAsset',
    ];
}