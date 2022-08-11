<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "registredmodelkey".
 *
 * @property int $Id
 * @property int $IdRegistredModel
 * @property string $AttributeKeyName
 * @property string $Description
 *
 * @property Registredmodel $registredModel
 */
class Registredmodelkey extends \yii\db\ActiveRecord
{
    private $model = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'registredmodelkey';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdRegistredModel', 'AttributeKeyName'], 'required'],
            [['IdRegistredModel'], 'integer'],
            [['Description'], 'string'],
            [['AttributeKeyName'], 'string', 'max' => 100],
            [['AttributeKeyName'],'unique','targetAttribute' => ['IdRegistredModel','AttributeKeyName'],'message' => 'El Campo {value} ya fue utilizado para esta llave'],
            [['IdRegistredModel'], 'exist', 'skipOnError' => true, 'targetClass' => Registredmodel::class, 'targetAttribute' => ['IdRegistredModel' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdRegistredModel' => 'Modelo',
            'AttributeKeyName' => 'Atributo Clave',
            'Description' => 'Descripción',
        ];
    }
    
    public function beforeDelete() {
        try {
            $this->_validateKeys();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredModel()
    {
        return $this->hasOne(Registredmodel::class, ['Id' => 'IdRegistredModel']);
    }
    
    public function getModelAttributes(){
        try {
            $attributes = [];
            if($this->IdRegistredModel){
                $model = Registredmodel::findOne(['Id' => $this->IdRegistredModel]);
                $attributes = $model->getModelAttributes();
                $this->model = $model->getModel();
            }
            return $attributes;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _validateKeys(){
        try {
            if(count($this->registredModel->registredmodelkeys) == 1){
                throw new Exception('No se puede Eliminar: Debe existir al menos un Atributo Clave',94000);
            }
        } catch (Exception $ex) {
            $this->addError('AttributeKeyName', $ex->getMessage());
            throw $ex;
        }
    }
}
