<?php

namespace backend\models\traits;

use common\models\Type;
use Yii;
use backend\models\Incidentcategory;
use common\models\State;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use moonland\phpexcel\Excel;
use Exception;

/* @var $this \backend\models\Incidentcategory */
trait Incidentcategorytrait
{
    private $defaultValues = [];
    private $_types = [];
    private $_categories = [];

    public function getGridList() :array {
        try {
            $this->options = [];
            foreach ($this->getTypes() as $key => $name){
                $this->item['Id'.$name] = null;
                $this->item['Icon'.$name] = null;
                $this->item[$name] = 'Default';
            }
            $this->_iterateChildren($this->_getChildren());
            //echo "<pre>";
            //print_r($this->options); die();
            return $this->options;
        } catch (Exception $exception){
            throw $exception;
        }
    }

    protected function _iterateChildren($items = [], $category = []){
        try {
            foreach ($items as $item){
                if( $item['CodType'] != self::KEYWORD_CATEGORY ){
                    $category[$item['CodType']] = $item['Name'];
                    $category['Id'.$item['CodType']] = $item['Id'];
                    $category['Code'.$item['CodType']] = $item['Code'];
                    $category['Icon'.$item['CodType']] = $item['ValType'];
                    $children = $this->_getChildren( ArrayHelper::getValue($item,'Id') );
                    if(empty($children)){
                        foreach ($this->item as $key => $value){
                            $category[$key] = ArrayHelper::getValue($category, $key, $value);
                        }
                        $this->options[ArrayHelper::getValue($item,'Id')] = ArrayHelper::merge($category, $this->attributes);
                    } else {
                        $category = ArrayHelper::merge($category, $item);
                        $this->_iterateChildren($children, $category);
                    }
                } else {
                    $this->options[ArrayHelper::getValue($item,'Id')] = ArrayHelper::merge($category, $item);
                }
            }
        } catch (Exception $exception){
            throw $exception;
        }
    }

    private function _getChildren( $idParent = null ){
        try{
            $query = new Query();
            $query->select([
                'a.Id',
                'a.Code',
                'a.Name',
                'a.IdState',
                'a.IdParent',
                'b.Name State',
                'c.Code CodeParent',
                'c.Name NameParent',
                'd.Code CodType',
                'd.Value ValType',
            ])
                ->from( self::tableName(). ' a')
                ->innerJoin( State::tableName().' b', 'b.Id = a.IdState')
                ->innerJoin( Type::tableName().' d', 'd.Id = a.IdType')
                ->leftJoin( self::tableName().' c',  'c.Id = a.IdParent')
                ->where([
                    'a.IdParent' => $idParent,
                ])
                ->orderBY([
                    'a.Id' => SORT_ASC
                ]);
            return $query->all();
        } catch ( Exception $exception){
            throw $exception;
        }
    }

    public function upload(): bool
    {
        set_time_limit(600);
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $this->_getDefaultData();
            $data = Excel::import($this->uploadFile->tempName, ['setFirstRecordAsKeys'=>TRUE,'setIndexSheetByName'=>TRUE,]);
            foreach ($data as $sheet => $lines){
                $model = new Incidentcategory();
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
                'KeyWord' => StringHelper::basename(Incidentcategory::class),
                'Code' => Incidentcategory::STATUS_ACTIVE
            ])->select('Id')->scalar();
            $this->_categories = ArrayHelper::map(self::find()->select(['Id','Code'])->asArray()->all(), 'Code', 'Id');

            $this->_types = ArrayHelper::map( Type::find()->select()->where([
                'Keyword' => StringHelper::basename( self::class)
            ])->asArray()->all(),  'Code', 'Id');
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
    private function _loadDefaultData(Incidentcategory &$model, $lines = []){
        try {
            $model->IdState = ArrayHelper::getValue($this->defaultValues, 'IdState');
            $model->IdType = $model->IdType ?: ArrayHelper::getValue($this->_types, ArrayHelper::getValue($lines, 'CodType'));
            $model->IdParent = ArrayHelper::getValue($lines, 'IdParent');
            $codParent = ArrayHelper::getValue($lines, 'CodeParent');
            if($codParent){
                $model->IdParent = ArrayHelper::getValue($this->_categories, $codParent);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}