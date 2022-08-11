<?php

namespace backend\models;
use backend\models\traits\Profileoptiontrait;
use common\models\Profile;
use backend\models\Option;
use kartik\helpers\Html;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
use yii\helpers\StringHelper;
use backend\components\AuthorizationFunctions;
use common\models\State;
use common\models\Type;
use Exception;

use Yii;

/**
 * This is the model class for table "profileoptions".
 *
 * @property integer $IdProfile
 * @property integer $IdOption
 * @property integer $Enabled
 *
 * @property Profile $profile
 * @property Option $option
 */
class Profileoption extends \yii\db\ActiveRecord
{
    use Profileoptiontrait;
    private static $_idProfile;
    private $profileoptions;
    private $children = NULL;
    public $permissions = [];
    public $list;
    private $auth;
    
    public $_idParent;
    
    function __construct($config = array()) {
        $this->auth = new AuthorizationFunctions();
        return parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profileoption';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdProfile', 'IdOption'], 'required'],
            [['IdProfile', 'IdOption', 'Enabled'], 'integer'],
            [['Enabled'],'default','value'=>1],
            [['IdProfile'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::class, 'targetAttribute' => ['IdProfile' => 'Id']],
            [['IdOption'], 'exist', 'skipOnError' => true, 'targetClass' => Option::class, 'targetAttribute' => ['IdOption' => 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdProfile' => 'Perfil',
            'IdOption' => 'Opción',
            'Enabled' => 'Habilitado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['Id' => 'IdProfile']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Option::class, ['Id' => 'IdOption']);
    }
    
    public static function getHtmlList($criteria = NULL){
        try {
            self::$_idProfile = isset($criteria['IdProfile']) ? $criteria['IdProfile']:NULL;
            unset($criteria['IdProfile']);
            $options = self::filterChildren(NULL, $criteria);
            $table = "";
            if($options == NULL){
                $table .= "<tr>"
                        . "<td colspan='10'>No se encontraron Registros</td>"
                        . "</tr>";
            } else {
                $table .= self::iterateChildren($options);
            }
            return $table;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function filterChildren($idparent = NULL, $criteria = []){
        try {
            $options = Option::find()
                ->joinWith(['profileoptions b'])
                ->joinWith([Type::tableName().' c'],false)
                ->where($criteria)
                ->andWhere(['IdParent'=>$idparent])
                ->select(Option::tableName().'.*, b.Enabled, c.Code')
                ->orderBy([Option::tableName().'.Sort'=>SORT_ASC])
                ->asArray()
                ->all();
            return $options;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function iterateChildren($options){
        try {
            $table = "";
            foreach ($options as $opt){
                $table .= self::getHtmlChildren($opt);
                $children = self::filterChildren($opt['Id']);
                if(!empty($children)){
                    $table.= self::iterateChildren($children);
                }
            }
            return $table;
        } catch (Exception $ex) {
            
        }
        
    }
    
    private static function getHtmlChildren($option = []){
        try {
            if(empty($option)){
                return "";
            }
            $opt = Option::findOne(['Id'=>$option['Id']]);
            $profileoption = self::findOne(['IdProfile'=> self::$_idProfile,'IdOption'=> $opt->Id]);
            $code = $opt->IdType ? $opt->type->Code:  Option::TYPE_PERMISSION;
            $table = "";
            $actions = "";
            switch ($code) {
                case Option::TYPE_MODULE:
                    $table = "<tr class='table-success'>";
                    $table .= "<td colspan='7'>"
                        . "<i class='$opt->Icon'></i> "
                        . $opt->Name;
                    $table .= "</td>";
                    break;
                case Option::TYPE_GROUP:
                    $table = "<tr class='table-danger'>";
                    $table .= "<td></td>"
                        . "<td colspan='4'>"
                        . "<i class='$opt->Icon'></i> "
                        . $opt->Name;
                    $table .= "</td>";
                    $table .= "<td>"
                            . ($opt->IdType ? $opt->type->Name:'')
                            . "</td>";
                    $table .= "<td>"
                            . $opt->Url
                            . "</td>";
                    break;
                case Option::TYPE_CONTROLLER:
                    $table = "<tr class='table-warning'>";
                    $table .= "<td colspan='2'></td>"
                        . "<td colspan='3'>"
                        . "<i class='$opt->Icon'></i> "
                        . $opt->Name;
                    $table .= "</td>";
                    $table .= "<td>"
                            . ($opt->IdType ? $opt->type->Name:'')
                            . "</td>";
                    $table .= "<td>"
                            . $opt->Url
                            . "</td>";
                    break;
                case Option::TYPE_ACTION:
                    $table = "<tr class='table-info'>";
                    $table .= "<td colspan='3'></td>"
                        . "<td colspan='2'>"
                        . "<i class='$opt->Icon'></i> "
                        . $opt->Name;
                    $table .= "</td>";
                    $table .= "<td>"
                            . ($opt->IdType ? $opt->type->Name:'')
                            . "</td>";
                    $table .= "<td>"
                            . $opt->Url
                            . "</td>";
                    break;
                case Option::TYPE_PERMISSION:
                default:
                    $parentType = ($opt->IdParent ? ($opt->parent->IdType ? $opt->parent->type->Code:Option::TYPE_ACTION):  Option::TYPE_ACTION);
                    $colspan = ($parentType == Option::TYPE_ACTION ? 4:3);
                    $colspanName = ($parentType == Option::TYPE_ACTION ? 1:2);
                    $table = "<tr>";
                    $table .= "<td colspan='$colspan'></td>"
                        . "<td colspan='$colspanName'>"
                        . "<i class='$opt->Icon'></i> "
                        . $opt->Name;
                    $table .= "</td>";
                    $table .= "<td>"
                            . ($opt->IdType ? $opt->type->Name:'')
                            . "</td>";
                    $table .= "<td>"
                            . $opt->Url
                            . "</td>";
                    break;
            }
            $table .= "<td>"
                    . $opt->KeyWord
                    . "</td>";
            $table .= "<td>"
                    . ($opt->IdState ? $opt->state->Name:"")
                    . "</td>";
            $table .= "<td>"
                    . ($opt->ItemMenu == 1 ? "SI":"NO")
                    . "</td>";
            $table .= "<td class='action-column'>"; 
            $tableName = StringHelper::basename(Profile::class) ."[".StringHelper::basename(self::class)."][".$opt->Id."]";
            $table .= Html::checkbox($tableName, ($profileoption ? ($profileoption->Enabled ? true:false):false), []);
            $table .= "</td>";
            $table .= "</tr>";
            return $table;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public static function getChildren($idparent = NULL, $criteria = []){
        try {
            $options = self::filterChildren($idparent, $criteria);
            $profileoptions = [];
            foreach ($options as $opt){
                $children = self::getChildren($opt["Id"], $criteria);
                $opt[self::tableName()] = $children;
                $profileoptions[$opt['KeyWord']] = $opt;
                $profileoptions[$opt['KeyWord']]['level']= StringHelper::basename(self::class);
            }
            return $profileoptions;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getMenuItems($opt = NULL){
        try {
            $children = self::find()
                    ->joinWith(Option::tableName().' b',true)
                    ->innerJoin(Optionenvironment::tableName().' c', 'c.IdOption = b.Id')
                    ->innerJoin(Type::tableName().' d', 'd.Id = c.IdEnvironmentType')
                    ->innerJoin(State::tableName().' e', 'e.Id = d.IdState')
                    ->where([
                        self::tableName().'.IdProfile'=>$opt->IdProfile,
                        'b.ItemMenu'=>true,
                        'b.IdParent'=>$opt->IdOption,
                        'd.KeyWord' => StringHelper::basename(Optionenvironment::class),
                        'd.Code' => StringHelper::basename(Yii::getAlias('@app')), 
                        'c.Enabled' => Optionenvironment::ENABLED_VALUE,
                        'e.KeyWord' => StringHelper::basename(Type::class),
                        'e.Code' => Type::STATUS_ACTIVE,
                    ])
                    
                    ->orderBy(['b.Sort'=>SORT_ASC])
                    ->all();
            $items = [];
            foreach ($children as $child){
                $items[$child->option->Sort] = $child;
            }
            return $items;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    public function getChildrenOptions($opt = NULL){
        try {
            $children = self::find()->joinWith('option b',true)
                    ->where(['IdProfile'=>$opt->IdProfile,'b.ItemMenu'=>TRUE,'b.IdParent'=>$opt->IdOption])
                    ->orderBy(['b.Sort'=>SORT_ASC])
                    ->all();
            $items = [];
            foreach ($children as $child){
                $item =[
                    'label'=>"<i class='".$child->option->Icon."'></i>&nbsp;".$child->option->Name,
                ];
                if($child->option->Url != NULL){
                    $url = '@web/'.$child->option->Url;
                    $item['url']= ($child->IdOption ? ($child->option->IdUrlType ? ($child->option->urlType->Code == Option::URL_OUTSIDE ? $child->option->Url:$url):$url):$url);
                } else {
                    $item['items']= $this->getChildrenOptions($child);
                }
                $items[$child->option->Sort] = $item;
            }
            return $items;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterSave($insert, $changedAttributes) {
        $this->_createByType();
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterDelete() {
        $this->_revoke();
        return parent::afterDelete();
    }
    
    public function _setPermissions(){
        try {
            if(!empty($this->permissions)){
                $this->_getProfileChildren();
                $this->_iterateActualPermissions();
                $this->_addNewPermissions();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getProfileChildren(){
        try {
            $this->profileoptions = self::find()->where(['IdProfile'=>  $this->IdProfile])->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getChildren(){
        try {
            $this->children = $this->IdOption ? $this->option->options:NULL; 
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateActualPermissions(){
        try {
            if(!empty($this->permissions)){
                foreach ($this->profileoptions as $opt){
                    if(!isset($this->permissions[$opt->IdOption])){
                        if(!$opt->delete()){
                            $message = $this->_gerErrors($opt->errors);
                            throw new \Exception($message, 98001);  
                        }
                    } 
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _addNewPermissions(){
        try {
            foreach ($this->permissions as $key => $value){
                $profileopt = self::findOne(['IdProfile'=> $this->IdProfile, 'IdOption'=> $key]);
                if(!$profileopt){
                    $this->_saveOption($key);
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _createByType(){
        try {
            $code = $this->IdOption ? ($this->option->IdType ? $this->option->type->Code:Option::TYPE_PERMISSION):Option::TYPE_PERMISSION;
            switch ($code) {
                case Option::TYPE_MODULE:
                case Option::TYPE_GROUP:
                case Option::TYPE_CONTROLLER:
                    break;
                case Option::TYPE_ACTION:
                case Option::TYPE_PERMISSION:
                default:
                    $this->_assignPermission();
                    break;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _assignModule(){
        try {
            $this->auth->assignRolePermission($this->profile->KeyWord, $this->option->KeyWord);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _assignPermission(){
        try {
            $this->auth->assignRolePermission($this->profile->KeyWord, $this->option->KeyWord);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _revoke(){
        try {
            $code = $this->IdOption ? ($this->option->IdType ? $this->option->type->Code:Option::TYPE_PERMISSION):Option::TYPE_PERMISSION;
            switch ($code) {
                case Option::TYPE_MODULE:
                case Option::TYPE_GROUP:
                case Option::TYPE_CONTROLLER:
                case Option::TYPE_ACTION:
                case Option::TYPE_PERMISSION:
                default:
                    $this->_revokePermission();
                    break;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _revokePermission(){
        try {
            $this->auth->removeRolePermission($this->profile->KeyWord, $this->option->KeyWord);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _saveOption($IdOption){
        try {
            $model = new Profileoption();
            $model->IdOption = $IdOption;
            $model->IdProfile = $this->IdProfile;
            if(!$model->save()){
                $message = $this->_gerErrors($model->errors);
                throw new \Exception($message, 92001);  
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _getErrors($errors = NULL){
        try {
            return StringHelper::basename(self::class).': '.\Yii::$app->components->customFunctions->getErrors($errors);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _getIndex(){
        try {
            $child = Option::findOne(['IdOption'=>  $this->IdOption,'KeyWord'=> $this->option->KeyWord.'Index']);
            return $child != NULL ? $child->Id:NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _getParent(){
        try {
            return $this->IdOption ? ($this->option->IdParent):NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
