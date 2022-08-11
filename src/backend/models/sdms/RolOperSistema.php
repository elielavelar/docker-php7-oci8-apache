<?php

namespace backend\models\sdms;

use Yii;

/**
 * This is the model class for table "PRDDUI.ROL_OPER_SISTEMA".
 *
 * @property int $COD_ROL
 * @property string $DESCRIPCION
 * @property string $ESTADO
 */
class RolOperSistema extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVE = 'I';
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.ROL_OPER_SISTEMA';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_ROL'], 'required'],
            [['COD_ROL'], 'integer'],
            [['DESCRIPCION'], 'string', 'max' => 15],
            [['ESTADO'], 'string', 'max' => 1],
            [['COD_ROL'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_ROL' => 'Código Rol',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
        ];
    }
}
