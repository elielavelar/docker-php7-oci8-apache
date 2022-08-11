<?php

namespace backend\models;

use yii;
use yii\base\Model;

class InfrastructurerequirementReport extends Model
{
    public $name;
    public $dateFrom;
    public $dateTo;
    
    public function rules()
    {
        return [
            [['name'], 'required'],           
        ];
    }
}