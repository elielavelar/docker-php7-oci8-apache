<?php

namespace backend\models\sdms;

use Yii;

/**
 * This is the model class for table "PRDDUI.CAT_CARGO_OPER".
 *
 * @property int $COD_CARGO_OPER
 * @property string $DESC_CARGO_OPER
 */
class CatCargoOper extends \yii\db\ActiveRecord
{
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.CAT_CARGO_OPER';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_CARGO_OPER'], 'required'],
            [['COD_CARGO_OPER'], 'integer'],
            [['DESC_CARGO_OPER'], 'string', 'max' => 40],
            [['COD_CARGO_OPER'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_CARGO_OPER' => 'Cod  Cargo  Oper',
            'DESC_CARGO_OPER' => 'Desc  Cargo  Oper',
        ];
    }
}
