<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use backend\models\CustomActiveRecord;
use yii\base\Exception;
use Yii;

/**
 * This is the model class for table "TRAM_VIGENTE".
 *
 * @property int $COD_OPER
 * @property string $PASWD_SISTEMA
 * @property string $PASWD_RED
 * @property string $NOM1_OPER
 * @property string $NOM2_OPER
 * @property string $NOM3_OPER
 * @property string $APDO1_OPER
 * @property string $APDO2_OPER
 * @property int $COD_ROL
 * @property int $COD_CARGO_OPER
 * @property int $COD_CTRO_SERV
 * @property string $COD_EMPLEADO
 * @property string $FECHA_CAMBIO
 *
 */

class DatosOper extends CustomActiveRecord {
    public static function getDb() {
        return Yii::$app->prdduitest;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.DATOS_OPER';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_OPER', 'NOM1_OPER', 'APDO1_OPER' , 'COD_ROL', 'COD_CARGO_OPER', 'STAT_OPER', 'COD_CTRO_SERV'], 'required'],
        ];
    }
}
