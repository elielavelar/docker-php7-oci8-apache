<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Infrastructurerequirementdetails;

/**
 * InfrastructurerequirementdetailSearch represents the model behind the search form of `backend\models\Infrastructurerequirementdetails`.
 */
class InfrastructurerequirementdetailSearch extends Infrastructurerequirementdetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdInfrastructureRequirement', 'IdUser', 'IdActivityType', 'IdRequirementState', 'IdAssignedUser', 'IdCatalogDetailValue'], 'integer'],
            [['Title', 'Description', 'DetailDate', 'RecordDate', 'SolutionDate', 'Commentaries'], 'safe'],
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
        $query = Infrastructurerequirementdetails::find();

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
            'IdInfrastructureRequirement' => $this->IdInfrastructureRequirement,
            'DetailDate' => $this->DetailDate,
            'RecordDate' => $this->RecordDate,
            'SolutionDate' => $this->SolutionDate,
            'IdUser' => $this->IdUser,
            'IdActivityType' => $this->IdActivityType,
            'IdRequirementState' => $this->IdRequirementState,
            'IdAssignedUser' => $this->IdAssignedUser,
            'IdCatalogDetailValue' => $this->IdCatalogDetailValue,
        ]);

        $query->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'Commentaries', $this->Commentaries]);

        return $dataProvider;
    }
}
