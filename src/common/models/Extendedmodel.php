<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;
use common\models\Registredmodel;
use backend\models\Settingdetail;
use common\models\State;
/**
 * This is the model class for table "extendedmodels".
 *
 * @property int $Id
 * @property int $IdRegistredModel
 * @property int $IdNameSpace
 * @property int $IdState
 * @property string $Description
 *
 * @property Extendedmodelkey[] $extendedmodelkeys
 * @property Registredmodel $registredModel
 * @property Settingdetail $nameSpace
 * @property State $state
 */
class Extendedmodel extends \yii\db\ActiveRecord
{
    public $keyword = null;
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    const _NAMESPACE_ = 'NameSpace';
    const _NAMESPACE_CODE_ = 'NESP';
    public $term = '';
    private $namespacePath = null;
    private $path = null;
    private $model = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdRegistredModel', 'IdNameSpace','IdState'], 'required'],
            [['IdRegistredModel','IdNameSpace', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['IdRegistredModel'], 'unique'],
            [['IdRegistredModel'], 'exist', 'skipOnError' => true, 'targetClass' => Registredmodel::class, 'targetAttribute' => ['IdRegistredModel' => 'Id']],
            [['IdNameSpace'], 'exist', 'skipOnError' => true, 'targetClass' => Settingdetail::class, 'targetAttribute' => ['IdNameSpace' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
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
            'IdNameSpace' => 'Espacio de Nombre',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
            'keyword' => 'Llave',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredModel()
    {
        return $this->hasOne(Registredmodel::class, ['Id' => 'IdRegistredModel']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelkeys()
    {
        return $this->hasMany(Extendedmodelkey::class, ['IdExtendedModel' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $states = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($states, 'Id', 'Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNameSpace()
    {
        return $this->hasOne(Settingdetail::class, ['Id' => 'IdNameSpace']);
    }
        
    public function getNameSpaces(){
        $settings = Settingdetail::find()
                ->joinWith('setting b')
                ->where([
                    'b.KeyWord' => self::_NAMESPACE_,
                    'b.Code' => self::_NAMESPACE_CODE_
                ])
                ->orderBy([Settingdetail::tableName().'.Sort' => SORT_ASC])
                ->all();
        return ArrayHelper::map($settings, 'Id', 'Name');
    }
    
    public function getNameSpaceByName($namespace = null){
        try {
            !$namespace ? StringHelper::basename(Yii::getAlias('@app')) : $namespace;
            $nspace = Settingdetail::find()
                ->joinWith('setting b')
                ->where([
                    'b.KeyWord' => self::_NAMESPACE_,
                    'b.Code' => self::_NAMESPACE_CODE_,
                    Settingdetail::tableName().'.Value' => $namespace,
                ])->one(); 
            return $nspace;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModels(){
        try {
            $nameSpace = Settingdetail::findOne(['Id' => $this->IdNameSpace]);
            $path = Yii::getAlias('@'.$nameSpace->Value);
            $this->term = empty($this->term) ? '': $this->term.'*';
            $files = glob($path.'/models/*'.$this->term.'.php');
            $result = [];
            foreach ($files as $i => $file){
                $filename = str_replace(".php", "", $file);
                $basename = StringHelper::basename($filename);
                $result[] = ['id' => $basename, 'text' =>$basename];
            }
            return ['results' => $result];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterFind() {
        $this->namespacePath = $this->IdNameSpace ? $this->nameSpace->Value : null;
        $this->keyword = $this->IdRegistredModel ? $this->registredModel->KeyWord : null;
        if($this->namespacePath){
            $this->path = $this->namespacePath."\models\\".$this->keyword;
            $this->model = new $this->path ;
        }
        return parent::afterFind();
    }

    public function getModelAttributes(){
        try {
            $attributes = [];
            foreach ($this->model->attributes as $key => $attr){
                $attributes[$key] = $this->model->getAttributeLabel($key);
            }
            return $attributes;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttributeLabel($key = null){
        try {
            return $this->model->getAttributeLabel($key);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getModelAttribute($key = null){
        try {
            return $this->model->getAttribute($key);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
