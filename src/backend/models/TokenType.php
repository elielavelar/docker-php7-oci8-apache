<?php
namespace backend\models;

use Yii;
use common\models\Type;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;

/**
 * Description of TokenType
 *
 * @author avelare
 */
class TokenType extends Type {
    
    const TOKEN_TYPE_SOFTWARE = 'SWTK';
    const TOKEN_TYPE_USERPASSWORD = 'USRPWDTK';
    
    public function beforeValidate() {
        $this->KeyWord = StringHelper::basename(self::class);
        return parent::afterValidate();
    }
    
    public static function find() {
        return parent::find()
                ->andWhere(['type.KeyWord' => StringHelper::basename(self::class)]);
    }
    
    public static function findAll($condition) {
        $condition['KeyWord'] = StringHelper::basename(self::class);
        return parent::findAll($condition);
    }
    
    public function beforeSave($insert) {
        $this->KeyWord = StringHelper::basename(self::class);
        return parent::beforeSave($insert);
    }
    
    public function getStates() {
        try {
            $droptions = State::findAll(['KeyWord'=>StringHelper::basename(parent::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function save($runValidation = true, $attributeNames = null) {
        $model = new Type();
        $model->attributes = $this->attributes;
        $response = $model->save();
        if(!$response) {
            $this->addErrors($model->errors);
        } else {
            $model->refresh();
            $this->attributes = $model->attributes;
            $this->Id = $model->Id;
        }
        return $response;
    }
    
}
