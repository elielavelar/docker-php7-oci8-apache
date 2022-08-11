<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\custom;

/**
 * Description of Application
 *
 * @author avelare
 */
class Application extends \yii\web\Application {
    public function coreComponents() {
        $components = parent::coreComponents();
        if(isset($components['view'])){
            $components['view'] = View::class;
        }
        return $components;
    }
}
