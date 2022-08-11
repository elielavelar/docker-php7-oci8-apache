<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Appointmentservicesetting;

/**
 * AppointmentservicesettingSearch represents the model behind the search form about `backend\models\Appointmentservicesetting`.
 */
class AppointmentservicesettingSearch extends Appointmentservicesetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdServiceCentre', 'IdDay', 'IdHour', 'IdState', 'Quantity'], 'integer'],
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
        $query = Appointmentservicesetting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Id' => $this->Id,
            'IdServiceCentre' => $this->IdServiceCentre,
            'IdDay' => $this->IdDay,
            'IdState' => $this->IdState,
            'Quantity' => $this->Quantity,
            'IdHour' => $this->IdHour,
        ]);

        #$query->andFilterWhere(['like', 'IdHour', $this->IdHour]);

        return $dataProvider;
    }
}
