<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fieldscatalogs;

/**
 * FieldscatalogsSearch represents the model behind the search form of `common\models\Fieldscatalogs`.
 */
class FieldscatalogsSearch extends Fieldscatalogs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdField', 'Sort', 'IdState'], 'integer'],
            [['Name', 'Value', 'Description'], 'safe'],
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
        $query = Fieldscatalogs::find();

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
            'IdField' => $this->IdField,
            'Sort' => $this->Sort,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Value', $this->Value])
            ->andFilterWhere(['like', 'Description', $this->Description]);
        
        $query->orderBy(['Sort' => SORT_ASC]);

        return $dataProvider;
    }
}
