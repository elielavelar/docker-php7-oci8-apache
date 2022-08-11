<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models\prddui;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "CAT_DEPTO".
 *
 * @property int $COD_DEPTO
 * @property string $NOM_DEPTO
 * @property string $COD_PAIS
 * 
 * @property Catpais $country
 */
class Catdepto extends ActiveRecord {
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.CAT_DEPTO';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_DEPTO', 'NOM_DEPTO', 'COD_PAIS'], 'required'],
            [['COD_DEPTO'], 'integer'],
            [['NOM_DEPTO'], 'string', 'max' => 50],
            [['COD_PAIS'], 'string', 'max' => 4],
            [['COD_DEPTO'], 'unique', 'targetAttribute' => ['COD_PAIS', 'COD_DEPTO'], 'message' => 'Ya existe el Código {value} para el país ingresado'],
            [['COD_PAIS'], 'exist', 'skipOnError' => true, 'targetClass' => Catpais::class, 'targetAttribute' => ['COD_PAIS' => 'COD_PAIS']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_DEPTO' => 'Departamento',
            'NOM_DEPTO' => 'Nombre',
            'COD_PAIS' => 'País',
        ];
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Catpais::class, ['COD_PAIS' => 'COD_PAIS']);
    }
}
