<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\components;
use Exception;
/**
 * Description of XMLResponse
 *
 * @author avelare
 */
class XMLResponse extends \yii\base\Component {
    public $charset;
    private $standalone = false;
    private $_includeStandalone = false;
    public $headers;
    public $data;
    public $content;
    public $dom;
    public $attributes = [];
    public $attributesExceptions = [];
    
    public function getCharset(){
        return $this->charset;
    }

    public function setStandalone($standalone = false){
        $this->standalone = $standalone;
    
    }
    public function setIncludeStandalone($standalone = false){
        $this->_includeStandalone = $standalone;
    }

    public function getIncludeStandalone(){
        return $this->_includeStandalone;
    }
    
    public function getStandalone(){
        return $this->standalone;
    }

    public function setCharset($charset = 'UTF-8'){
        $this->charset = $charset;
    }

    public function getHeaders(){
        return $this->headers;
    }
    
    public function setHeaders($headers){
        $this->headers = $headers;
    }
    
    public function appendAttribute($attribute = NULL){
        array_push($this->attributes, $attribute);
    }
    
    public function getAttributes(){
        return $this->attributes;
    }
    
    public function setAttributes($attributes = []){
        $this->attributes = $attributes;
    }
    
    public function isAttribute($attribute = NULL){
        try {
            return in_array($attribute, $this->attributes);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function appendAttributesException($attribute = NULL){
        array_push($this->attributesExceptions, $attribute);
    }
    
    public function getAttributesException(){
        return $this->attributesExceptions;
    }
    
    public function setAttributesExceptions($attributes = []){
        $this->attributesExceptions = $attributes;
    }
    
    public function isAttributeException($attribute = NULL){
        try {
            return in_array($attribute, $this->attributesExceptions);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
}
