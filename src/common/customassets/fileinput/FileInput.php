<?php

namespace common\customassets\fileinput;
use common\customassets\fileinput\BootstrapIconsAsset;
use kartik\file\FileInputAsset;
use kartik\file\FileInputThemeAsset;
use kartik\file\PiExifAsset;
use kartik\file\SortableAsset;
use yii\base\InvalidConfigException;
use Exception;
use yii\helpers\ArrayHelper;

class FileInput extends \kartik\widgets\FileInput
{
    /**
     * Registers the asset bundle and locale
     * @throws InvalidConfigException|Exception
     */
    public function registerAssetBundle()
    {
        $view = $this->getView();
        $this->pluginOptions['resizeImage'] = $this->resizeImages;
        $this->pluginOptions['autoOrientImage'] = $this->autoOrientImages;
        if ($this->resizeImages || $this->autoOrientImages) {
            PiExifAsset::register($view);
        }
        if (empty($this->pluginOptions['theme'])) {
            if ($this->isBs(3)) {
                $this->pluginOptions['theme'] = 'gly';
            } else {
                BootstrapIconsAsset::register($view);
            }
        }
        $theme = ArrayHelper::getValue($this->pluginOptions, 'theme');
        if (!empty($theme) && in_array($theme, self::$_themes)) {
            FileInputThemeAsset::register($view)->addTheme($theme);
        }
        if ($this->sortThumbs) {
            SortableAsset::register($view);
        }
        FileInputAsset::register($view)->addLanguage($this->language, '', 'js/locales');
    }
}