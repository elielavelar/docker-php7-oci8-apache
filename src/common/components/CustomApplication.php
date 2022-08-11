<?php
namespace backend\components;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomApplication
 *
 * @author avelare
 */
class CustomApplication extends \yii\web\Application {
    /**
     * Returns the user component.
     * @return User the user component.
     */
    public function getCountry()
    {
        return $this->get('country');
    }
    /**
     * {@inheritdoc}
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'country' => [ 'class' => 'backend\components\CountryUser' ],
        ]);
    }
}
