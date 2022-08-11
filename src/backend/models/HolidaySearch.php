<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Holidays;

/**
 * HolidaySearch represents the model behind the search form about `common\models\Holidays`.
 */
class HolidaySearch extends Holidays
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdType', 'IdState', 'IdFrequencyType'], 'integer'],
            [['Name', 'Description', 'DateStart', 'DateEnd'], 'safe'],
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
        $query = Holidays::find();

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
            'IdType' => $this->IdType,
            'IdState' => $this->IdState,
            'DateStart' => $this->DateStart,
            'DateEnd' => $this->DateEnd,
            'IdFrequencyType' => $this->IdFrequencyType,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
