<?php

namespace backend\models\sdms;

use Yii;

/**
 * This is the model class for table "SDMS_SALVADOR_MOCKUP.TSEGURIDAD".
 *
 * @property string $COD_OPER
 * @property string $PASWD_SISTEMA
 * @property string $COD_CTRO_SERV
 */
class TSeguridad extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return Yii::$app->prddui;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SDMS_SALVADOR_MOCKUP.TSEGURIDAD';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_OPER'], 'required'],
            [['COD_OPER', 'COD_CTRO_SERV'], 'string', 'max' => 8],
            [['PASWD_SISTEMA'], 'string', 'max' => 150],
            [['COD_OPER'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_OPER' => 'Cod  Oper',
            'PASWD_SISTEMA' => 'Paswd  Sistema',
            'COD_CTRO_SERV' => 'Cod  Ctro  Serv',
        ];
    }
}
