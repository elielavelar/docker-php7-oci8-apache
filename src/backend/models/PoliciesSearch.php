<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Policies;

/**
 * PoliciesSearch represents the model behind the search form of `backend\models\Policies`.
 */
class PoliciesSearch extends Policies
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Code', 'IdProcess', 'IdType', 'IdState', 'IdUser'], 'integer'],
            [['Name', 'Description'], 'safe'],
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
        $query = Policies::find();

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
            'Code' => $this->Code,
            'IdProcess' => $this->IdProcess,
            'IdType' => $this->IdType,
            'IdState' => $this->IdState,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
