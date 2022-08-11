<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Incident;

/**
 * IncidentSearch represents the model behind the search form of `backend\models\Incident`.
 */
class IncidentSearch extends Incident
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'Ticket', 'IdServiceCentre', 'IdReportUser', 'IdCategoryType', 'IdInterruptType', 'IdPriorityType', 'IdRevisionType', 'IdState', 'Commentaries', 'IdUser'], 'integer'],
            [['IncidentDate', 'TicketDate', 'InterruptDate', 'SolutionDate'], 'safe'],
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
        $query = Incident::find();

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
            'IncidentDate' => $this->IncidentDate,
            'TicketDate' => $this->TicketDate,
            'InterruptDate' => $this->InterruptDate,
            'SolutionDate' => $this->SolutionDate,
            'IdServiceCentre' => $this->IdServiceCentre,
            'IdReportUser' => $this->IdReportUser,
            'IdCategoryType' => $this->IdCategoryType,
            'IdInterruptType' => $this->IdInterruptType,
            'IdPriorityType' => $this->IdPriorityType,
            'IdRevisionType' => $this->IdRevisionType,
            'IdState' => $this->IdState,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'Commentaries', $this->Commentaries]);

        return $dataProvider;
    }
}
