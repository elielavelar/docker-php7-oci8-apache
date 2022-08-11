<?php

namespace backend\models\traits;

use backend\models\Option;
use backend\models\Profileoption;

/** @var $this Profileoption */
trait Profileoptiontrait
{

    public static function checkAccess($permissionName){
        $user = \Yii::$app->getUser()->getIdentity();
        $option = Profileoption::find()
            ->joinWith(Option::tableName().' b')
            ->where(['b.KeyWord'=> $permissionName])
            ->andWhere([Profileoption::tableName().'.IdUser'=> $user->Id])
            ->one();
        return ( !empty($option)?: $option->Enabled );
    }
}