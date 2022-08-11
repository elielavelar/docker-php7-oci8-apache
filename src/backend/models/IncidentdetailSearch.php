<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Incidentdetail;

/**
 * IncidentdetailSearch represents the model behind the search form of `backend\models\Incidentdetail`.
 */
class IncidentdetailSearch extends Incidentdetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdIncident', 'IdSupportType', 'IdProblemType', 'IdActivityType', 'IdEvaluatorUser', 'IdEvaluationValue', 'IdIncidentState', 'IdAssignedUser', 'IdUser'], 'integer'],
            [[ 'Description', 'DetailDate', 'RecordDate', 'OnSiteDate', 'SolutionDate', 'TicketProv', 'CodEquipment', 'Commentaries'], 'safe'],
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
        $query = Incidentdetail::find();

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
            'IdIncident' => $this->IdIncident,
            'DetailDate' => $this->DetailDate,
            'RecordDate' => $this->RecordDate,
            'OnSiteDate' => $this->OnSiteDate,
            'SolutionDate' => $this->SolutionDate,
            'IdSupportType' => $this->IdSupportType,
            'IdProblemType' => $this->IdProblemType,
            'IdActivityType' => $this->IdActivityType,
            'IdEvaluatorUser' => $this->IdEvaluatorUser,
            'IdEvaluationValue' => $this->IdEvaluationValue,
            'IdIncidentState' => $this->IdIncidentState,
            'IdAssignedUser' => $this->IdAssignedUser,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'TicketProv', $this->TicketProv])
            ->andFilterWhere(['like', 'CodEquipment', $this->CodEquipment])
            ->andFilterWhere(['like', 'Commentaries', $this->Commentaries]);

        return $dataProvider;
    }
}
