<?php

namespace backend\models\traits;

use Yii;
use backend\models\Problemtype;
use common\models\State;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use moonland\phpexcel\Excel;
use Exception;

/* @property $this Problemtype */
trait Problemtypetrait
{
    private $defaultValues = [];
    private $_componentTypes = [];

    public function upload(): bool
    {
        set_time_limit(600);
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $this->_getDefaultData();
            $data = Excel::import($this->uploadFile->tempName, ['setFirstRecordAsKeys'=>TRUE,'setIndexSheetByName'=>TRUE,]);
            foreach ($data as $sheet => $lines){
                $model = new Problemtype();
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
                'KeyWord' => StringHelper::basename(Problemtype::class),
                'Code' => Problemtype::STATE_ACTIVE
            ])->select('Id')->scalar();
            $this->_componentTypes = array_flip($this->getComponentTypes('Code'));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /**
     *
     * @param Problemtype $model
     * @param array $lines
     * @throws Exception
     */
    private function _loadDefaultData(Problemtype &$model, $lines = []){
        try {
            $model->IdState = ArrayHelper::getValue($this->defaultValues, 'IdState');
            $model->IdActiveType = $this->IdActiveType ?: ArrayHelper::getValue($lines, 'IdActiveType');
            $model->IdComponentType = $this->IdComponentType ?: ArrayHelper::getValue($lines, 'IdComponentType');
            $codeComponent = ArrayHelper::getValue($lines, 'CodeComponent');
            if($codeComponent){
                $model->IdComponentType = ArrayHelper::getValue($this->_componentTypes, $codeComponent);
            }
            if(empty($model->IdComponentType)){
                $model->IdComponentType = ArrayHelper::getValue($this->_componentTypes, self::COMPONENT_UNDEFINED);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}