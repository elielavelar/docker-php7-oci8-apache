<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\custom;
use Yii;
use yii\web\JsExpression;
/**
 * Description of View
 *
 * @author avelare
 */
class View extends \yii\web\View {
    //put your code here
    public function render($view, $params = array(), $context = null) {
        $controller = Yii::$app->controller->id;
        return parent::render($view, $params, $context);
    }
}
