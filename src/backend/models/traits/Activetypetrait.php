<?php

namespace backend\models\traits;

use backend\models\Problemtype;
use Yii;
use backend\models\Activetype;
use backend\models\Incidentcategory;
use common\models\State;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use moonland\phpexcel\Excel;
use Exception;

/* @property $this Activetype */
trait Activetypetrait
{
    private $defaultValues = [];
    private $_categories = [];

    public function upload(): bool
    {
        set_time_limit(600);
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $this->_getDefaultData();
            $data = Excel::import($this->uploadFile->tempName, ['setFirstRecordAsKeys'=>TRUE,'setIndexSheetByName'=>TRUE,]);
            foreach ($data as $sheet => $lines){
                $model = new Activetype();
                $model->attributes = $lines;
                $this->_loadDefaultData($model, $lines);
                if(!$model->save()){
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    $this->addError('uploadFile',$message);
                    throw new Exception($message, 95000);
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    private function _getDefaultData(){
        try {
            $this->defaultValues['IdState'] = (int) State::find()->where([
                'KeyWord' => StringHelper::basename(Activetype::class),
                'Code' => Activetype::STATUS_ACTIVE
            ])->select('Id')->scalar();
            $this->_categories = array_flip($this->getCategorytypes());
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /**
     *
     * @param Incidentcategory $model
     * @param array $lines
     * @throws Exception
     */
    private function _loadDefaultData(Activetype &$model, $lines = []){
        try {
            $model->IdState = ArrayHelper::getValue($this->defaultValues, 'IdState');
            $model->IdCategoryType = $this->IdCategoryType ?: ArrayHelper::getValue($lines, 'IdCategoryType');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}