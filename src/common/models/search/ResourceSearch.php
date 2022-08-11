<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Resource;

/**
 * ResourceSearch represents the model behind the search form of `common\models\Resource`.
 */
class ResourceSearch extends Resource
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdType', 'IdResourceType', 'IdServiceCentre', 'IdState', 'IdUserCreation', 'IdUserLastUpdate', 'IdParent'], 'integer'],
            [['Name', 'Code', 'CreationDate', 'LastUpdateDate', 'Description'], 'safe'],
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
        $query = Resource::find();

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
            'IdResourceType' => $this->IdResourceType,
            'IdServiceCentre' => $this->IdServiceCentre,
            'IdState' => $this->IdState,
            'CreationDate' => $this->CreationDate,
            'IdUserCreation' => $this->IdUserCreation,
            'LastUpdateDate' => $this->LastUpdateDate,
            'IdUserLastUpdate' => $this->IdUserLastUpdate,
            'IdParent' => $this->IdParent,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
