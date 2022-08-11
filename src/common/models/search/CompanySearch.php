<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Company;

/**
 * CompanySearch represents the model behind the search form of `common\models\Company`.
 */
class CompanySearch extends Company
{
    function __construct() {
        $this->searchModel = true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdSizeType', 'Enabled'], 'integer'],
            [['Name', 'TaxRegistrationNumber', 'TaxIdentificationNumber', 'TradeName', 'BusinessSector', 'Description'], 'safe'],
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
        $query = Company::find();

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
            'IdSizeType' => $this->IdSizeType,
            'Enabled' => $this->Enabled,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'TaxRegistrationNumber', $this->TaxRegistrationNumber])
            ->andFilterWhere(['like', 'TaxIdentificationNumber', $this->TaxIdentificationNumber])
            ->andFilterWhere(['like', 'TradeName', $this->TradeName])
            ->andFilterWhere(['like', 'BusinessSector', $this->BusinessSector])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
