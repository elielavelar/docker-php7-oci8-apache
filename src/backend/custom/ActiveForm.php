<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\custom;

use Yii;
/**
 * Description of ActiveForm
 *
 * @author avelare
 */
class ActiveForm extends \kartik\widgets\ActiveForm {
    public $withFormTag = true;
    
    /**
     * Runs the widget.
     * This registers the necessary JavaScript code and renders the form open and close tags.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching.
     */
    public function run()
    {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        if($this->withFormTag){
            $html = Html::beginForm($this->action, $this->method, $this->options);
        }
        $html .= $content;

        if ($this->enableClientScript) {
            $this->registerClientScript();
        }
        if($this->withFormTag){
            $html .= Html::endForm();
        }
        return $html;
    }
    //put your code here
}
