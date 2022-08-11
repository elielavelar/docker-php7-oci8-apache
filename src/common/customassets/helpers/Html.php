<?php

namespace common\customassets\helpers;

use yii\helpers\ArrayHelper;

class Html extends \yii\bootstrap4\Html
{
    /**
     * @throws \Exception
     */
    public static function icon($icon = 'fas fa-cube', $options = []){
        $options['class'] = $icon.' '.ArrayHelper::getValue($options, 'class','');
        return self::tag('i', null, $options);
    }
}