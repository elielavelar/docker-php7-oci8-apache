<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointment;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * AppointmentsSearch represents the model behind the search form about `common\models\Appointments`.
 */
class AppointmentSearch extends Appointment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdState', 'IdServiceCentre','IdType','IdRegistrationMethod', 'IdBirthPlace', 'IdClientUser'], 'integer'],
            [['FirstName','SecondName','ThirdName','LastName','SecondLastName','completeName','Code','ShortCode'], 'string'],
            [['AppointmentDate','completeName','Code','ShortCode'
                #'AppointmentHour',
                #,'hourDate'
                ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Appointment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(),[
                'completeName' => [
                    'asc' => ['FirstName' => SORT_ASC, 'LastName' => SORT_ASC],
                    'desc' => ['FirstName' => SORT_DESC, 'LastName' => SORT_DESC],
                    'label' => 'Ciudadano',
                    'default' => SORT_ASC
                ],
            ]),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Appointment::tableName().'.Id' => $this->Id,
            Appointment::tableName().'.IdState' => $this->IdState,
            Appointment::tableName().'.IdType' => $this->IdType,
            Appointment::tableName().'.ShortCode' => $this->ShortCode,
            Appointment::tableName().'.IdServiceCentre' => $this->IdServiceCentre,
            Appointment::tableName().'.IdRegistrationMethod' => $this->IdRegistrationMethod,
            Appointment::tableName().'.IdBirthPlace' => $this->IdBirthPlace,
            Appointment::tableName().'.IdCountry' => $this->IdCountry,
            Appointment::tableName().'.IdClientUser' => $this->IdClientUser,
        ]);
        if(!empty($this->AppointmentDate)){
            $query->andWhere("AppointmentDate = :fecha",[':fecha'=> date_format(new \DateTime($this->AppointmentDate),'Y-m-d')]);
        }
        if(!empty($this->AppointmentHour)){
            $query->andWhere("date_format(AppointmentDate,'%H') = :hora",[':hora'=> \Yii::$app->formatter->asTime($this->AppointmentHour,'php:H')]);
        }
        $query->andFilterWhere(['like', 'UPPER('.Appointment::tableName().'.FirstName)', mb_strtoupper($this->FirstName)])
            ->andFilterWhere(['like', 'UPPER('.Appointment::tableName().'.SecondName)',mb_strtoupper($this->SecondName)])
            ->andFilterWhere(['like', 'UPPER('.Appointment::tableName().'.LastName)', mb_strtoupper($this->LastName)])
            ->andFilterWhere(['like', 'UPPER('.Appointment::tableName().'.SecondLastName)', mb_strtoupper($this->SecondLastName)]);

        if(!empty($this->FirstName)){
            $query->andWhere(
                " UPPER( CONCAT(".Appointment::tableName().".FirstName, ( CASE WHEN TRIM(".Appointment::tableName().".SecondName) != '' THEN TRIM(".Appointment::tableName().".SecondName) ELSE '' END ) ) ) LIKE '%" . mb_strtoupper($this->FirstName)  . "%'" //This will filter when full name is searched.
            );
        }
        if(!empty($this->LastName)){
            $query->andWhere(
                " UPPER( CONCAT(".Appointment::tableName().".LastName, ( CASE WHEN TRIM(".Appointment::tableName().".LastName) != '' THEN TRIM(".Appointment::tableName().".LastName) ELSE '' END ) ) ) LIKE '%" . mb_strtoupper($this->LastName)  . "%'" //This will filter when full name is searched.
        );
        }

        if(empty(ArrayHelper::getValue($params,'sort'))){
            $query->orderBy(['AppointmentDate'=>'ASC', 'AppointmentHour'=>'ASC']);
        }

        return $dataProvider;
    }
}
