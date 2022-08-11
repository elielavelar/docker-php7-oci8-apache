<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\components;

/**
 * Description of XMLResponse
 *
 * @author avelare
 */
class XMLResponse extends \yii\base\Component {
    public $charset;
    public $headers;
    public $data;
    public $content;
    public $dom;
    public $attributes = [];
    public $attributesExceptions = [];
    
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
