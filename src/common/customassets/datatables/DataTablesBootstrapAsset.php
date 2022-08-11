<?php

namespace common\customassets\datatables;

class DataTablesBootstrapAsset extends \sdelfi\datatables\DataTablesBootstrapAsset
{
    public $depends = [
        //'yii\web\JqueryAsset',
        'common\customassets\datatables\DataTablesAsset',
    ];
}