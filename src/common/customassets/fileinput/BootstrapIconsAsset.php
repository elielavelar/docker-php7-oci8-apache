<?php

namespace common\customassets\fileinput;

class BootstrapIconsAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/bootstrap-icons';
    public $basePath = '@web';
    /**
     * @inheritDoc
     */
    public $css = ['font/bootstrap-icons.css'];
}