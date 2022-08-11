<?php

namespace common\components\HttpClient;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use Exception;

class CurlClient implements HttpInterface
{
    protected $model;
    public $url;
    protected $headers = [];
    protected $data = [];
    protected $options = [];
    protected $request;
    protected $response;
    protected $transport;

    /**
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $this->url = ArrayHelper::getValue($config, 'url');
        $this->data = ArrayHelper::getValue($config, 'data', []);
        $this->headers = ArrayHelper::getValue($config, 'headers', []);
        $this->options = ArrayHelper::getValue($config, 'options', []);
        $this->transport = ArrayHelper::getValue($config, 'transport', 'yii\httpclient\CurlTransport');
        $this->model = new Client([
            'transport' => $this->transport,
        ]);
    }

    public function setData($data = []){
        $this->data = $data;
    }

    public function setHeaders($headers = []){
        $this->headers = $headers;
    }

    public function find(array $params = []){
        try {
            $this->url = ArrayHelper::getValue($params, 'url', $this->url);
            $this->data = ArrayHelper::getValue($params, 'data', $this->data);
            $this->headers = ArrayHelper::getValue($params, 'headers', $this->headers);
            $this->options = ArrayHelper::getValue($params, 'options', $this->options);
            $this->request = $this->model->get($this->url, $this->data, $this->headers, $this->options);
            $this->response = $this->request->send();
            return Json::decode($this->response->content);
        } catch (Exception $exception ){
            throw $exception;
        }
    }

    private function _prepareRequest($params){
        try {
            $this->url = ArrayHelper::getValue($params, 'url', $this->url);
            $this->data = ArrayHelper::getValue($params, 'data', $this->data);
            $this->headers = ArrayHelper::getValue($params, 'headers', $this->headers);
            $this->options = ArrayHelper::getValue($params, 'options', $this->options);
        } catch (Exception $exception){
            throw $exception;
        }
    }

    private function _sendRequest(){
        try {
            $this->response = $this->request->send();
            return Json::decode($this->response->content);
        } catch (Exception $exception){
            throw $exception;
        }
    }

    public function get(array $params = []){
        try {
            $this->_prepareRequest($params);
            $this->request = $this->model->get($this->url, $this->data, $this->headers, $this->options);
            return $this->_sendRequest();
        } catch (Exception $exception ){
            throw $exception;
        }
    }

    public function post(array $params = []){
        try {
            $this->_prepareRequest($params);
            $this->request = $this->model->post($this->url, $this->data, $this->headers, $this->options);
            return $this->_sendRequest();
        } catch (Exception $exception ){
            throw $exception;
        }
    }

    public function put(array $params = []){}

    public function head(array $params = []){}

    public function delete(array $params = []){}

    public function options(array $params = []){}

    public function patch(array $params = []){
        try {
            $this->_prepareRequest($params);
            $this->request = $this->model->patch($this->url, $this->data, $this->headers, $this->options);
            return $this->_sendRequest();
        } catch (Exception $exception ){
            throw $exception;
        }
    }

    public function runRequest( $method = null, array $params = []){
        try {
            if( !$method || !method_exists( $this->model ,$method) ){
                throw new Exception("Method invalid or not defined", 99000);
            }
            $this->_prepareRequest($params);
            $this->request = call_user_func_array([ $this->model, $method], [$this->url, $this->data, $this->headers, $this->options]);
            return $this->_sendRequest();
        } catch (Exception $exception ){
            throw $exception;
        }
    }

}