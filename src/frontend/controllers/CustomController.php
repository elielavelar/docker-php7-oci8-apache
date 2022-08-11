<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace frontend\controllers;

use Yii;
use common\components\BasicController;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use backend\models\Option;
/**
 * Description of CustomController
 *
 * @author Eliel Avelar <ElielAbisai.AvelarJaimes@muehlbauer.de>
 */
class CustomController extends BasicController {
    //put your code here
    public $customactions = [];
    private $auth;
    private $_actionname;
    private $_option = null;
    private $_user = null;


    public function actions() {
        $actions = parent::actions();
        return $actions;
    }

    public function __construct($id, $module, $config = array()) {

        $this->auth = \Yii::$app->authManager;
        return parent::__construct($id, $module, $config);
    }

    public function setCustomActions($customactions = []){
        $this->customactions = array_merge($this->customactions, $customactions);
    }

    public function beforeAction($action) {
        try {
            $parent = parent::beforeAction( $action );
            !$parent ? $this->redirect(['site/login']) : null;
            (!\Yii::$app->getUser()->isGuest ) ? ( (empty(Yii::$app->getSession()->get('itemsMenu')) || empty(Yii::$app->getSession()->get('subMenu'))) ? \Yii::$app->getUser()->getIdentity()->getUserMenu() : null ) : null;
            if(!in_array($this->action->id, $this->customactions)){
                if(!\Yii::$app->user->isGuest){
                    $this->_user = \Yii::$app->user->getIdentity();
                    if($this->_user->expired){
                        return $this->redirect(['site/changepassword']);
                    }
                }
                $this->_actionname = $this->id.ucfirst($this->action->id);
                $_action = $this->auth->getPermission($this->_actionname);
                if(empty($_action)){
                    $_action = $this->auth->createPermission($this->_actionname);
                    $_action->description = 'Permiso para Accion '.$this->_actionname;
                    $this->auth->add($_action);
                    #$this->auth->addChild($_controller, $_action);
                }
                $this->_option = Option::findOne(['KeyWord' => $this->_actionname]);
                if(!empty($this->_option)){
                    if($this->_option->RequireAuth == Option::REQUIRE_AUTH_TRUE) {
                        if($this->_user == null || empty(Yii::$app->getSession())){
                            $this->goHome();
                        }
                        if(!\Yii::$app->customFunctions->userCan($this->_actionname)){
                            $message = 'No posee permisos para la acción: '.$this->id.' / '.$this->_actionname;
                            throw new ForbiddenHttpException($message);
                        }
                    }
                }
            }
            return $parent;
        } catch( InvalidConfigException $exception){
            $this->redirect('site/index');
        } catch( Throwable $exception ){
            throw $exception;
        }
    }

    public function _getActionName(){
        return $this->_actionname;
    }

    public function setMessageErrors($errors = []){
        return $this->getErrors($errors);
    }

    public function viewErrors($errors){
        if(!empty($errors)){
            foreach ($errors as $error){
                $message = (implode("- ", $error));
                Yii::$app->session->setFlash('error', $message);
            }
        }
    }
}
