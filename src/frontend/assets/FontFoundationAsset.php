<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\assets;
use yii\web\AssetBundle;

/**
 * Description of FontFoundationAsset
 *
 * @author avelare
 */
class FontFoundationAsset extends AssetBundle {
    public $sourcePath = '@vendor/foundation-icons/';

    /**
     * @var array
     */
    public $css = [
        #'css/font-awesome.css'
        'foundation-icons.css'
    ];
}
