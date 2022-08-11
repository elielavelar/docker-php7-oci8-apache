<?php

namespace backend\components;

use Yii;
use yii\base\Component;

/**
 * Description of AuthorizationFunctions
 *
 * @author Eliel Avelar <ElielAbisai.AvelarJaimes@muehlbauer.de>
 */
class AuthorizationFunctions extends Component {
    private $auth;
    
    function __construct($config = array()) {
        $this->auth = \Yii::$app->authManager;
        return parent::__construct($config);
    }
    
    /*ROLES*/
    
    public function createRole($name, $description = NULL){
        try {
            $role = $this->auth->createRole($name);
            $role->description = $description;
            return $this->auth->add($role);
        } catch (Exception $ex) {
            throw new $ex;
        }
    }
    
    public function getRole($name){
        try {
            return $this->auth->getRole($name);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function assignRole($userId, $name){
        try {
            $role = $this->getRole($name);
            return $this->auth->assign($role, $userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function revokeRole($userId, $name){
        try {
            $role = $this->getRole($name);
            return $this->auth->revoke($role, $userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function revokeAllRoles($userId){
        try {
            return $this->auth->revokeAll($userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeAllAssignments($name){
        try {
            $role = $this->getRole($name);
            return $this->auth->removeChildren($role);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    public function assignRolePermission($rolename, $name){
        try {
            $parent = $this->getRole($rolename);
            $child = $this->getPermission($name);
            return $this->auth->addChild($parent, $child);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeRolePermission($rolename, $name){
        try {
            $parent = $this->getRole($rolename);
            $child = $this->getPermission($name);
            return $this->auth->removeChild($parent, $child);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    /*MODULE*/
    public function createModule($name){
        try {
            $permission = $this->auth->createPermission($name);
            return $this->auth->add($permission);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeModule($name){
        try {
            $permission = $this->auth->getPermission($name);
            $this->removeChildren($permission->name);
            return $this->auth->remove($permission);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /*GROUP*/
    public function createGroup($name, $parentname){
        try {
            $parent = $this->auth->getPermission($parentname);
            $controller = $this->auth->createPermission($name);
            $this->auth->add($controller);
            return $this->auth->addChild($parent, $controller);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeGroup($name, $parentname){
        try {
            $permission = $this->auth->getPermission($name);
            $this->removeChildren($permission->name);
            return $this->auth->remove($permission);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /*CONTROLLER*/
    public function createController($name, $parentname){
        try {
            $parent = $this->auth->getPermission($parentname);
            $controller = $this->auth->createPermission($name);
            $this->auth->add($controller);
            return $this->auth->addChild($parent, $controller);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeController($name, $parentname){
        try {
            $permission = $this->auth->getPermission($name);
            $this->removeChildren($permission->name);
            return $this->auth->remove($permission);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /*ACTIONS*/
    public function createAction($name, $parentname){
        try {
            return $this->createController($name, $parentname);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function removeAction($name, $parentname){
        try {
            return $this->removeController($name, $parentname);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getAction($name){
        try {
            return $this->getPermission($name);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /*PERMISSION*/
    
    public function createPermission($name, $parentname){
        try {
            return $this->createController($name, $parentname);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removePermission($name, $parentname){
        try {
            return $this->removeController($name, $parentname);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getPermission($name){
        try {
            return $this->auth->getPermission($name);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeChildren($name){
        try {
            $children = $this->auth->getChildren($name);
            if(!empty($children)){
                foreach ($children as $child){
                    $this->removeChildren($child->name);
                    $this->auth->remove($child);
                }
            } else {
                $permission = $this->getPermission($name);
                $this->auth->remove($permission);
            }
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function removeChild($parentname, $childname){
        try {
            $parent = $this->getPermission($parentname);
            $child = $this->getPermission($childname);
            return $this->auth->removeChild($parent, $child);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function updatePermission($name, $option){
        try {
            return $this->auth->update($name, $option);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function checkAccess($userId, $permissionName){
        try {
            return $this->auth->checkAccess($userId, $permissionName);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    /*USER*/
    
    public function getUserRoles($userId){
        try {
            return $this->auth->getRolesByUser($userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getUserRole($userId, $roleName){
        try {
            return $this->auth->getAssignment($roleName, $userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getUserAssignments($userId){
        try {
            return $this->auth->getAssignments($userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function revokeAll($userId){
        try {
            return $this->auth->revokeAll($userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function assignUserPermission($userid, $name){
        try {
            $child = $this->getPermission($name);
            return $this->auth->assign($child, $userid);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function revokeUserPermission($userid, $name){
        try {
            $child = $this->getUserPermission($userid, $name);
            return $this->auth->removeChild($userid, $child);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getUserPermission($userid, $name){
        try {
            return $this->auth->getAssignment($name, $userId);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
}
