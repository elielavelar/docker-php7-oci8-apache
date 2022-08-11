<?php

namespace common\models;
use common\models\CustomActiveRecord;
use Yii;
/**
 * This is the model class for table "state".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property string $Value
 * @property int $Sort
 * @property string $Description
 *
 * @property Type[] $types
 */
class State extends CustomActiveRecord
{
    public $create;
    public $update;
    public $delete;
    public $view;
    
    private $controller = NULL;
    const CONTROLLER_NAME = 'state';
    const DEFAULT_SORT = 1;
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->controller = !empty(\Yii::$app->controller) ? \Yii::$app->controller->id: NULL;
    }
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code','Sort'], 'required'],
            [['Sort'],'integer'],
            [['Name', 'KeyWord'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 10],
            [['Value'], 'string', 'max' => 20],
            [['Description'], 'string', 'max' => 1000],
            [['Code'], 'unique', 'targetAttribute' => ['KeyWord', 'Code'], 'message' => 'Ya existe el Código {value} para la llave ingresada'],
            ['Sort','default', 'value' => self::DEFAULT_SORT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'KeyWord' => 'Llave',
            'Code' => 'Código',
            'Value' => 'Valor',
            'Sort' => 'Orden',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::className(), ['IdState' => 'Id']);
    }
    
    public function afterFind() {
        if($this->controller == self::CONTROLLER_NAME ){
            $this->create = \Yii::$app->user->can(self::tableName().'Create');
            $this->update = \Yii::$app->user->can(self::tableName().'Update');
            $this->delete = \Yii::$app->user->can(self::tableName().'Delete');
            $this->view = \Yii::$app->user->can(self::tableName().'View');
        }
        return parent::afterFind();
    }

    public static function get($keyword = null, $code = null, $asArray = false, $callback = false){
        $query = self::find()
            ->where([
                'KeyWord' => $keyword,
                'Code' => $code,
            ]);
        $asArray ? $query->asArray() : null;
        $result = $query->one();
        return is_callable($callback) ? $callback( $result ) : $result;
    }

    public static function getAll($keyword = null, $asArray = false, $callback = false){
        $query = self::find()
            ->where([
                'KeyWord' => $keyword,
            ]);
        $asArray ? $query->asArray() : null;
        $result = $query->all();
        return is_callable($callback) ? $callback( $result ) : $result;
    }
}
