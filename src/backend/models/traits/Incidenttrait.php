<?php

namespace backend\models\traits;

use common\models\Attachment;
use yii\helpers\StringHelper;

/* @var $this \backend\models\Incident */
trait Incidenttrait
{
    protected function saveFiles(){
        try {
            $model = new Attachment();
            $model->KeyWord = StringHelper::basename(self::class);
            $model->AttributeName = 'Id';
            $model->AttributeValue = (string) $this->Id;
            $model->fileattachment = $this->fileattachment;
            if(!$model->saveFiles()){
                $message = \Yii::$app->customFunctions->getErrors($model->getErrors());
                throw new \Exception($message, 99000 );
            }
        } catch (\Exception $exception){
            throw $exception;
        }
    }
}