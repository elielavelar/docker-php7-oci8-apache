<?php

namespace backend\models\traits;

use backend\models\Option;
use backend\models\Useroption;

/** @var $this Useroption */
trait Useroptiontrait
{
    public static function checkAccess($permissionName){
        $user = \Yii::$app->getUser()->getIdentity();
        $option = self::find()
            ->joinWith(Option::tableName().' b')
            ->where(['b.KeyWord'=> $permissionName])
            ->andWhere([self::tableName().'.IdUser'=> $user->Id])
            ->one();
        return ( !empty($option)?: $option->Enabled );
    }
}