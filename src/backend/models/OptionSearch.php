<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Option;

/**
 * OptionSearch represents the model behind the search form about `backend\models\Options`.
 */
class OptionSearch extends Option
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdState', 'IdType', 'IdParent', 'Sort', 'ItemMenu'], 'integer'],
            [['Name', 'KeyWord', 'Icon', 'Url', 'Description'], 'safe'],
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
        $query = Option::find();

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
            'IdState' => $this->IdState,
            'IdType' => $this->IdType,
            'IdParent' => $this->IdParent,
            'Sort' => $this->Sort,
            'ItemMenu' => $this->ItemMenu,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'KeyWord', $this->KeyWord])
            ->andFilterWhere(['like', 'Icon', $this->Icon])
            ->andFilterWhere(['like', 'Url', $this->Url])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
