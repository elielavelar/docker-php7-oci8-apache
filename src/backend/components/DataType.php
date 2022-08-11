<?php
namespace backend\components;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DataType extends \yii\base\Component {
    const TYPE_GENERAL = 0;
    const TYPE_GENERAL_CODE = 'general';
    const TYPE_STRING = 1;
    const TYPE_STRING_CODE = 'string';
    const TYPE_NUMBER = 2;
    const TYPE_NUMBER_CODE = 'number';
    const TYPE_DATE = 3;
    const TYPE_DATE_CODE = 'date';
    const TYPE_LONG_STRING = 4;
    const TYPE_LONG_STRING_CODE = 'longstring';
    const TYPE_DATE_RANGE = 5;
    const TYPE_DATE_RANGE_CODE = 'daterange';
    const TYPE_VALUE_RANGE = 6;
    const TYPE_VALUE_RANGE_CODE = 'valuerange';
    const TYPE_HOUR = 7;
    const TYPE_HOUR_CODE = 'hour';
    const TYPE_COLOR = 8;
    const TYPE_COLOR_CODE = 'color';
    const TYPE_SWITCH = 9;
    const TYPE_SWITCH_CODE = 'switch';
    const TYPE_MASK = 10;
    const TYPE_MASK_CODE = 'mask';
    const TYPE_JSEXPRESSION = 11;
    const TYPE_JSEXPRESSION_CODE = 'js';
}