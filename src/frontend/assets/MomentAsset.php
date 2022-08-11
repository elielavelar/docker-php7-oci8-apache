<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\assets;
use yii\web\AssetBundle;

class MomentAsset extends AssetBundle {
    public $sourcePath = '@bower/moment';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'min/moment.min.js',
        'min/locales.min.js',
    ];
}
