<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "incidentattachment".
 *
 * @property int $Id
 * @property int $IdIncident
 * @property string $FileName
 * @property string $Description
 * @property string $Commentaries
 * @property string $CreationDate
 * @property int $IdUser
 *
 * @property Incident $incident
 * @property User $user
 */
class Incidentattachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incidentattachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdIncident', 'FileName', 'IdUser'], 'required'],
            [['IdIncident', 'IdUser'], 'integer'],
            [['Commentaries'], 'string'],
            [['CreationDate'], 'safe'],
            [['FileName'], 'string', 'max' => 250],
            [['Description'], 'string', 'max' => 500],
            [['IdIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Incident::className(), 'targetAttribute' => ['IdIncident' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdIncident' => 'Incidencia',
            'FileName' => 'Nombre de Archivo',
            'Description' => 'Descripcion',
            'Commentaries' => 'Comentarios',
            'CreationDate' => 'Fecha de Creación',
            'IdUser' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncident()
    {
        return $this->hasOne(Incident::className(), ['Id' => 'IdIncident']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'IdUser']);
    }
}
