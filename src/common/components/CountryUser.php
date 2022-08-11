<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\di\Instance;
use common\models\Country;
/**
 * Description of Country
 *
 * @author avelare
 */
class CountryUser extends Component {
    private $_identity = false;
    public $idParam = '__idcountry';
    public $autoRenewCookie = true;
    public $enableSession = true;
    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;
    
    public function setCountry(Country $identity){
        $this->switchIdentity($identity);
        return !empty($this->getIdentity());
    }


    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }
    /**
     * Returns the identity object associated with the currently logged-in user.
     * When [[enableSession]] is true, this method may attempt to read the user's authentication data
     * stored in session and reconstruct the corresponding identity object, if it has not done so before.
     * @param bool $autoRenew whether to automatically renew authentication status if it has not been done so before.
     * This is only useful when [[enableSession]] is true.
     * @return IdentityInterface|null the identity object associated with the currently logged-in user.
     * `null` is returned if the user is not logged in (not authenticated).
     * @see login()
     * @see logout()
     */
    public function getIdentity($autoRenew = true)
    {
        if ($this->_identity === false) {
            if ($this->enableSession && $autoRenew) {
                try {
                    $this->_identity = null;
                    $this->renewAuthStatus();
                } catch (\Exception $e) {
                    $this->_identity = false;
                    throw $e;
                } catch (\Throwable $e) {
                    $this->_identity = false;
                    throw $e;
                }
            } else {
                return;
            }
        }
        return $this->_identity;
    }
    
    public function switchIdentity($identity, $duration = 0)
    {
        $this->setIdentity($identity);

        if (!$this->enableSession) {
            return;
        }
        $session = Yii::$app->getSession();
        $session->remove($this->idParam);

        if ($identity) {
            $session->set($this->idParam, $identity->getId());
        }
    }
    
    protected function renewAuthStatus()
    {
        $session = Yii::$app->getSession();
        $id = $session->getHasSessionId() || $session->getIsActive() ? $session->get($this->idParam) : null;

        if ($id === null) {
            $identity = null;
        } else {
            $class = $this->identityClass;
            $identity = $class::findIdentity($id);
        }

        $this->setIdentity($identity);
    }
}
