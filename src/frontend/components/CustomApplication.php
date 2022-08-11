<?php
namespace frontend\components;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use common\models\Company;
use common\models\Country;

/**
 * Description of CustomApplication
 *
 * @author avelare
 */
class CustomApplication extends \yii\web\Application {
    /**
     * Returns the user component.
     * @return Country  the user component.
     */
    public function getCountry()
    {
        return $this->get('country');
    }

    /**
     * Returns the user component.
     * @return Company the user component.
     */
    public function getCompany()
    {
        return $this->get('company');
    }
    /**
     * {@inheritdoc}
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'country' => [ 'class' => 'common\components\CountryUser' ],
            'company' => [ 'class' => 'common\components\CompanyUser' ],
        ]);
    }
}
