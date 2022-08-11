<?php
namespace backend\models;

use Yii;
use common\models\Type;
use Throwable;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * Description of TokenPermission
 *
 * @author avelare
 */
class TokenPermission extends Type {
 
    public function beforeValidate() {
        $this->KeyWord = StringHelper::basename(self::class);
        return parent::afterValidate();
    }
    
    public static function find() {
        parent::setAttribute('KeyWord', StringHelper::basename(self::class));
        return parent::find();
    }
    
    public static function findAll($condition) {
        $condition['KeyWord'] = StringHelper::basename(self::class);
        return parent::findAll($condition);
    }
    public static function findOne($condition) {
        $condition['KeyWord'] = StringHelper::basename(self::class);
        return parent::findOne($condition);
    }
    
    public function beforeSave($insert) {
        $this->KeyWord = StringHelper::basename(self::class);
        return parent::beforeSave($insert);
    }
    
    public function save($runValidation = true, $attributeNames = null) {
        $response = parent::save($runValidation, $attributeNames);
        #if(!$response){
            print_r(parent::getErrors()); die();
        #} 
        return $response;
    }


    public function afterSave($insert, $changedAttributes) {
        try {
            if(!parent::afterSave($insert, $changedAttributes)){
                
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }
    
    private function _getErrors(){
        try {
            $errors = parent::getErrors();
            if(!empty($errors)){
                $this->errors = $errors;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
