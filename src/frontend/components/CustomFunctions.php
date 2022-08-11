<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\components;
use yii\base\Component;
use Exception;
use yii\db\Query;

/**
 * Description of CustomFunctions
 *
 * @author Eliel Avelar <ElielAbisai.AvelarJaimes@muehlbauer.de>
 */
class CustomFunctions extends Component {
    //put your code here
    private $weekdays = [
        '1'=>'Domingo',
        '2'=>'Lunes',
        '3'=>'Martes',
        '4'=>'Miércoles',
        '5'=>'Jueves',
        '6'=>'Viernes',
        '7'=>'Sábado',
    ];
    
    private $months = [
        '1'=>'Enero',
        '2'=>'Febrero',
        '3'=>'Marzo',
        '4'=>'Abril',
        '5'=>'Mayo',
        '6'=>'Junio',
        '7'=>'Julio',
        '8'=>'Agosto',
        '9'=>'Septiembre',
        '10'=>'Octubre',
        '11'=>'Noviembre',
        '12'=>'Diciembre',
    ];
    
    public function getMonthName($month = NULL){
        return isset($this->months[$month]) ? $this->months[$month]:NULL;
    }
    
    public function getMonths(){
        return $this->months;
    }
    
    public function getMonthNames(){
        $_months = [];
        foreach ($this->months as $id => $month){
            array_push($_months, $month);
        }
        return $_months;
    }

    public function getWeekDays(){
        return $this->weekdays;
    }
    
    public function getDayName($day = NULL){
        return in_array($day, $this->weekdays) ? $this->weekdays[$day]:NULL;
    }
    
    public function getErrors($errors){
        $errorMessage = "";
        if(!empty($errors)){
            foreach ($errors as $error){
                $message = (implode("- ", $error));
                #Yii::$app->session->setFlash('error', $message);
                $errorMessage .= $message."<br/>";
            }
        }
        return $errorMessage;
    }
    
    public function getDefaultActions($actions = ['ADD'=>[],'DEL'=>[]]){
        try {
            $default = [
                'index', 'create','update','delete','view'
            ];
            foreach ($actions['ADD'] as $act){
                array_push($default, $act);
            }
            foreach ($actions['DEL'] as $act){
                unset($default[$act]);
            }
            return $default;
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    public function getRandomPass($start = null, $length = null, $special = TRUE, $case = 0 ){
            try{
                $length = ($length == null ? 12:$length);
                if($start == null){
                    $pass = self::getRandomString($length, $special, $case);
                } else {
                    $pass = $start.self::getRandomString($length-strlen($start), $special, $case);
                    
                }
                return $pass;
            } catch(Exception $exc){
                throw $exc;
            }
        }
        
        public function getRandomString($length = null, $special = TRUE, $case = 0){
            try{
                $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $numbers = '1234567890';
                $symbols = '@*-+.&$#=_!';
                $pass = []; //remember to declare $pass as an array
                $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                $numbersLength = strlen($numbers) - 1;
                $symbolsLength = strlen($symbols) - 1;
                $hasUpper = FALSE;
                $hasSpecial = FALSE;
                $hasLower = FALSE;
                if(!$length){
                    $length = 12;
                }
                $specialPos = rand(2, $length);
                $upperPos = rand(2, $length);
                $lowerPos = rand(2, $length);
                $init = $special ? 0:1;
                for ($i = 0; $i < $length; $i++) {
                    if($i < 2){
                        $l = 2;
                    } elseif($i == $specialPos && (!$hasSpecial)){
                        $l = 0;
                    } else {
                        $l = rand($init,2);
                    }
                    switch($l){
                        case 0:
                            $n = rand(0, $symbolsLength);
                            $pass[] = $symbols[$n];
                            $hasSpecial = TRUE;
                            break;
                        case 1:
                            $n = rand(0, $numbersLength);
                            $pass[] = $numbers[$n];
                            break;
                        case 2:
                        default:
                            $n = rand(0, $alphaLength);
                            $pass[] = ($i== $upperPos && !$hasUpper) ? strtoupper($alphabet[$n]) : $alphabet[$n];
                            $hasUpper = ($i== $upperPos) ? TRUE:$hasUpper;
                            $hasLower = ($n == $lowerPos || $n < 27) && $n != $upperPos ? TRUE:$hasLower;
                            break;
                    }
                } 
                if(!$hasLower){
                    $pass[] = $alphabet[rand(0,27)];
                }
                    
                $string = implode($pass); //turn the array into a string
                switch ($case) {
                    case 1:
                        $return = strtolower($string);
                        break;
                    case 2:
                        $return = strtoupper($string);
                        break;
                    default:
                        $return = $string;
                        break;
                }
                return $return;
            } catch(Exception $exc) {
                throw $exc;
            }
        }
        
        public static function applyQueryCriteria(&$query , $criteria = []){
            try {
                /*
                 * @var $query Query
                 */
                if(isset($criteria["SPECIALCOND"])){
                    foreach ($criteria["SPECIALCOND"] as $cond){
                        $query->andWhere($cond);
                    }
                    unset($criteria["SPECIALCOND"]);
                }
                foreach ($criteria as $key => $value){
                    switch (gettype($value)) {
                        case 'array':
                            self::applyQueryCriteria($query, $value);
                            break;
                        default:
                            $query->andWhere([$key => $value]);
                            break;
                    }
                }
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        
        public static function userCan($permissionName = NULL){
            try {
                if(\Yii::$app->user->isGuest){
                    return false;
                }
                $can = FALSE;
                $useroption = FALSE;
                $custom = FALSE;
                $user = \Yii::$app->user->getIdentity();
                $option = \backend\models\Useroption::find()
                        ->joinWith('option b')
                        ->where(['b.KeyWord'=> $permissionName])
                        ->andWhere([\backend\models\Useroption::tableName().'.IdUser'=> $user->Id])
                        ->one();
                if(!empty($option)){
                    $custom = TRUE;
                    $useroption = $option->Enabled;
                } else {
                    $useroption = 0;
                }
                if($custom){
                    $can = $useroption == 1;
                } else {
                    $can = \Yii::$app->user->can($permissionName);
                }
                return $can;
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        
        public static function getAttributeChanges($oldAttributes = [], $newAttributes = []){
            try {
                $diff = [];
                foreach ($oldAttributes as $key => $value){
                    if($newAttributes[$key] != $value){
                        $diff[$key] = $newAttributes[$key];
                    }
                }
                return $diff;
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        
        public static function getAttributeDiff($oldAttributes = [], $newAttributes = []){
            try {
                $diff = [];
                if(!empty($oldAttributes)){
                    foreach ($oldAttributes as $key => $value){
                        if($newAttributes[$key] != $value){
                            $diff[$key] = ['VALUE'=> $newAttributes[$key],'OLDVALUE' => $value];
                        }
                    }
                } else {
                    foreach ($newAttributes as $key => $value){
                        $diff[$key] = ['VALUE' => $value,'OLDVALUE' => NULL];
                    }
                }
                return $diff;
            } catch (Exception $ex) {
                throw $ex;
            }
        }
    
}
