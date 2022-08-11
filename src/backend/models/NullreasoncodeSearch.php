<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Nullreasoncodes;

/**
 * NullreasoncodeSearch represents the model behind the search form of `backend\models\Nullreasoncodes`.
 */
class NullreasoncodeSearch extends Nullreasoncodes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdNullReason', 'IdState'], 'integer'],
            [['Name', 'Code', 'CodDevol', 'Description'], 'safe'],
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
        $query = Nullreasoncodes::find();

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
            'IdNullReason' => $this->IdNullReason,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'CodDevol', $this->CodDevol])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
