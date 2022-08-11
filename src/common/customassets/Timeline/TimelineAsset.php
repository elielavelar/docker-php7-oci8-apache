<?php

namespace common\customassets\Timeline;

class TimelineAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@common/customassets/Timeline/assets/';
    public $js = [
        //'js/timeline.js',
    ];
    public $css = [
        'css/adminlte.extra-components.css'
    ];
    public $depends = [
    ];
}