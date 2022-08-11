<?php
namespace backend\models\traits;

use common\models\State;
use common\models\Type;
use Yii;
use backend\models\Option;
use yii\db\Query;
use Exception;
use yii\helpers\ArrayHelper;

trait Optiontrait
{
    protected $items = [];
    protected $options = [];
    private $item = [];

    public function getOptionList() : array {
        try {
            $this->options = [];
            foreach ($this->getTypes() as $key => $name){
                $this->item['Id'.$name] = null;
                $this->item['Icon'.$name] = null;
                $this->item[$name] = 'Default';
            }
            $this->_iterateChildren($this->_getOptions());
            return $this->options;
        } catch (Exception $exception){
            throw $exception;
        }
    }

    protected function _iterateChildren($items = [], $category = []){
        try {
            foreach ($items as $item){
                if(!in_array($item['Code'], [Option::TYPE_PERMISSION, Option::TYPE_ACTION])){
                    $category[$item['Type']] = $item['Name'];
                    $category['Id'.$item['Type']] = $item['Id'];
                    $category['Icon'.$item['Type']] = $item['Icon'];
                    $children = $this->_getOptions(ArrayHelper::getValue($item,'Id'));
                    if(empty($children)){
                        foreach ($this->item as $key => $value){
                            $category[$key] = ArrayHelper::getValue($category, $key, $value);
                        }
                        $this->options[ArrayHelper::getValue($item,'Id')] = ArrayHelper::merge($category, $this->attributes);
                    } else {
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

    protected function _getOptions($idOption = null){
        try {
            $query = new Query();
            return $query->select([
                    'a.Id','a.Name', 'a.KeyWord', 'a.IdType', 'd.Name Type', 'd.Code', 'a.Icon','a.Url',
                    'a.IdState', 'c.Name State','a.IdUrlType', 'c.Name UrlType', 'a.IdParent',
                    'a.Sort', 'a.ItemMenu', 'a.RequireAuth', 'a.Require2StepAuth', 'a.SaveLog', 'a.SaveTransaction'
                ])
                ->from( Option::tableName().' a')
                ->leftJoin( State::tableName().' c', 'c.Id = a.IdState')
                ->leftJoin( Type::tableName().' d', 'd.Id = a.IdType')
                ->leftJoin( Type::tableName().' e', 'e.Id = a.IdUrlType')
                ->where([
                    'a.IdParent' => $idOption,
                ])
                ->orderBy([
                    'a.IdParent' => SORT_ASC, 'a.Sort' => SORT_ASC,
                ])
                ->all();

        } catch (Exception $exception){
            throw $exception;
        }
    }
}