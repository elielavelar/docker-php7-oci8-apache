<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\City;

/**
 * CitySearch represents the model behind the search form of `common\models\City`.
 */
class CitySearch extends City
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdCountryState', 'Enabled'], 'integer'],
            [['Name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = City::find();

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
            'IdCountryState' => $this->IdCountryState,
            'Enabled' => $this->Enabled,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name]);

        return $dataProvider;
    }
}
