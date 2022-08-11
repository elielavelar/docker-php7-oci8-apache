<?php

namespace common\models\traits;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use backend\models\Incidentcategory;
use common\models\Catalogdetail;
use common\models\Catalogdetailvalue;
use common\models\Catalogversion;
use common\models\Catalog;

trait Resourcetrait
{
    public $baseUrl;

    /**
     * @var $class \common\components\HttpClient\CurlClient
     * @throws Exception
     */
    public function getApiResource(){
        try {
            $config = ArrayHelper::getValue(\Yii::$app->params['api'], 'resource', []);
            $this->baseUrl = ArrayHelper::getValue($config, 'url');
            $class = ArrayHelper::getValue( $config, 'class');
            $client = new $class([
                'url' => $this->baseUrl.'/resource/'.$this->TokenId
            ]);
            $this->details = $client->runRequest('get');
        } catch (Exception $exception ){
            throw $exception;
        }
    }

    public function saveResourceApi(){
        try {
            $config = ArrayHelper::getValue(\Yii::$app->params['api'], 'resource', []);
            $this->baseUrl = ArrayHelper::getValue($config, 'url');
            $class = ArrayHelper::getValue( $config, 'class');
            $data = [
                'type' => $this->IdResourceType ? $this->resourceType->KeyWord : '',
                'name' => $this->Name,
                'code' => $this->Code,
                'token' => $this->TokenId,
                'active' => $this->IdState && $this->state->Code == self::STATUS_ACTIVE
            ];
            if( $this->_isNewRecord ){
                $url = $this->baseUrl.'/resource';
                $method = 'post';
            } else {
                $url = $this->baseUrl.'/resource/'.$this->TokenId;
                $method = 'patch';
            }
            $client = new $class([
                'url' => $url,
                'data' => $data
            ]);
            $client->runRequest( $method );
        } catch (Exception $exception){
            throw $exception;
        }
    }

    public function getCategoryList(): array{
        try {
            $categories = Incidentcategory::find()->where();
        } catch (Exception $exception){
            throw $exception;
        }
    }
}