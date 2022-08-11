<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Extendedmodelfield;

/**
 * ExtendedmodelfieldSearch represents the model behind the search form of `common\models\Extendedmodelfields`.
 */
class ExtendedmodelfieldSearch extends Extendedmodelfield
{ 
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdExtendedModelKey', 'IdField', 'Required', 'Sort', 'ColSpan', 'RowSpan'], 'integer'],
            [['CustomLabel', 'CssClass', 'Description'], 'safe'],
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
        $query = Extendedmodelfields::find();

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
            'IdExtendedModelKey' => $this->IdExtendedModelKey,
            'IdField' => $this->IdField,
            'Required' => $this->Required,
            'Sort' => $this->Sort,
            'ColSpan' => $this->ColSpan,
            'RowSpan' => $this->RowSpan,
        ]);

        $query->andFilterWhere(['like', 'CustomLabel', $this->CustomLabel])
            ->andFilterWhere(['like', 'CssClass', $this->CssClass])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
