<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\assets;
use yii\web\AssetBundle;

/**
 * Description of FontelloAsset
 *
 * @author avelare
 */
class FontelloAsset extends AssetBundle {
     public $sourcePath = '@vendor/fontello/';

    /**
     * @var array
     */
    public $css = [
        #'css/font-awesome.css'
        'css/fontello.min.css'
    ];
}
