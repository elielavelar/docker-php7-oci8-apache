<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Infrastructurerequirement;

/**
 * InfrastructurerequirementSearch represents the model behind the search form of `backend\models\Infrastructurerequirement`.
 */
class InfrastructurerequirementSearch extends Infrastructurerequirement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Ticket', 'IdServiceCentre', 'IdIncident', 'IdState', 'IdInfrastructureRequirementType', 'IdReportUser', 'IdUser', 'AffectsFunctionality', 'AffectsSecurity', 'Quantity', 'IdPriorityType', 'IdCreateUser'], 'integer'],
            [['TicketDate', 'RequirementDate', 'SolutionDate', 'Title', 'DamageDescription', 'SpecificLocation', 'Description'], 'safe'],
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
        $query = Infrastructurerequirement::find();

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
            'Ticket' => $this->Ticket,
            'TicketDate' => $this->TicketDate,
            'RequirementDate' => $this->RequirementDate,
            'SolutionDate' => $this->SolutionDate,
            'IdServiceCentre' => $this->IdServiceCentre,
            'IdIncident' => $this->IdIncident,
            'IdState' => $this->IdState,
            'IdInfrastructureRequirementType' => $this->IdInfrastructureRequirementType,
            'IdReportUser' => $this->IdReportUser,
            'IdUser' => $this->IdUser,
            'AffectsFunctionality' => $this->AffectsFunctionality,
            'AffectsSecurity' => $this->AffectsSecurity,
            'Quantity' => $this->Quantity,
            'IdPriorityType' => $this->IdPriorityType,
            'IdCreateUser' => $this->IdCreateUser,
        ]);

        $query->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'DamageDescription', $this->DamageDescription])
            ->andFilterWhere(['like', 'SpecificLocation', $this->SpecificLocation])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
