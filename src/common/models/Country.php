<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "countries".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $Code
 * @property integer $IdState
 *
 * @property State $state
 */
class Country extends CustomActiveRecord
{
    
    public $view;
    public $create;
    public $update;
    public $delete;
    
    private $controller = NULL;

    const CONTROLLER_NAME = 'country';
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->controller = !empty(\Yii::$app->controller) ? \Yii::$app->controller->id: NULL;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'Code', 'IdState'], 'required'],
            [['IdState'], 'integer'],
            [['Name'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 10],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }
    
    public function afterFind() {
        return parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'Code' => 'Código',
            'IdState' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        try {
            $droptions = State::findAll(['KeyWord'=>StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
}
