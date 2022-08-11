<?php

namespace common\components;

use \Yii;
use yii\log\Logger;

class AppLog extends Logger
{
    const CATEGORY_APPLICATION = 'application';
    public static function setLog($message, $level = 'info', $category = self::CATEGORY_APPLICATION){
        $_message = (gettype($message) == 'array') ? Yii::$app->customFunctions->getErrors($message) : $message;
        Yii::$app->getLog()->getLogger()->log($_message, self::getLevelError($level), $category);
    }

    public static function getLevelError($level){
        switch($level){
            case 'warning':
                return self::LEVEL_WARNING;
            case 'error':
                return self::LEVEL_ERROR;
            case 'profileend':
                return self::LEVEL_PROFILE_END;
            case 'profilebegin':
                return self::LEVEL_PROFILE_BEGIN;
            case 'trace':
                return self::LEVEL_TRACE;
            case 'profile':
                return self::LEVEL_PROFILE;
            case 'info':
            default:
                return self::LEVEL_INFO;
        }
    }
}