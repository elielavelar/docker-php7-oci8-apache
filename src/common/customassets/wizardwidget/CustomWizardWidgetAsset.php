<?php
namespace common\customassets\wizardwidget;
use yii\web\AssetBundle;

/**
 * Description of CustomWizardWidgetAsset
 *
 * @author AVELARE
 */
class CustomWizardWidgetAsset extends AssetBundle {
    public $sourcePath = '@common/customassets/wizardwidget';
    public $depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap4\BootstrapPluginAsset'
    ];
    public $css = [
            'css/wizardwidget.css',
    ];
    public $js = [
            'js/wizardwidget.js'
    ];
}
