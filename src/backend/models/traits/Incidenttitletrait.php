<?php

namespace backend\models\traits;


use Yii;
use Exception;
use moonland\phpexcel\Excel;
use backend\models\Incidenttitle;

/**
 *
 * @author avelare
 *
 * @var $this Incidenttitle
 */
trait Incidenttitletrait
{
    public function upload()
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $data = Excel::import($this->uploadFile->tempName, ['setFirstRecordAsKeys' => TRUE, 'setIndexSheetByName' => TRUE,]);
            foreach ($data as $sheet => $lines) {
                $detail = new Incidenttitle();
                $detail->attributes = $lines;

                if (!$detail->save()) {
                    $message = Yii::$app->customFunctions->getErrors($detail->errors);
                    throw new Exception($message, 93000);
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

}
