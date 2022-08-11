<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Securityincident;
use yii\helpers\StringHelper;

/**
 * SecurityincidentSearch represents the model behind the search form of `backend\models\Securityincident`.
 */
class SecurityincidentSearch extends Securityincident
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Ticket', 'IdServiceCentre', 'IdIncident', 'IdReportUser', 'IdType', 'IdState', 'IdLevelType', 'IdPriorityType', 'IdInterruptType', 'IdUser', 'IdCreateUser', 'IdCategoryType'], 'integer'],
            [['TicketDate', 'IncidentDate', 'InterruptDate', 'SolutionDate', 'Title', 'Description','Year'], 'safe'],
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
        $query = Securityincident::find();

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

        $query->innerJoin('type b', 'b.Id = '.self::tableName().".IdType");
        $query->where([
            'b.KeyWord' => StringHelper::basename(Securityincident::class),
        ]);
        
        // grid filtering conditions
        $query->andFilterWhere([
            'securityincident.Id' => $this->Id,
            'securityincident.Ticket' => $this->Ticket,
            'securityincident.InterruptDate' => $this->InterruptDate,
            'securityincident.SolutionDate' => $this->SolutionDate,
            'securityincident.IdServiceCentre' => $this->IdServiceCentre,
            'securityincident.IdIncident' => $this->IdIncident,
            'securityincident.IdReportUser' => $this->IdReportUser,
            'securityincident.IdType' => $this->IdType,
            'securityincident.IdState' => $this->IdState,
            'securityincident.IdLevelType' => $this->IdLevelType,
            'securityincident.IdPriorityType' => $this->IdPriorityType,
            'securityincident.IdInterruptType' => $this->IdInterruptType,
            'securityincident.IdUser' => $this->IdUser,
            'securityincident.IdCreateUser' => $this->IdCreateUser,
            'securityincident.IdCategoryType' => $this->IdCategoryType,
        ]);
        if(!empty($this->TicketDate)){
            $query->andWhere("date_format(TicketDate,'%Y-%m-%d') = :fecha",[':fecha'=> date_format(new \DateTime($this->TicketDate),'Y-m-d')]);
        }
        if(!empty($this->IncidentDate)){
            $query->andWhere("date_format(IncidentDate,'%Y-%m-%d') = :fecha",[':fecha'=> date_format(\DateTime::createFromFormat('d-m-Y', $this->IncidentDate),'Y-m-d')]);
        }
        if(!empty($this->InterruptDate)){
            $query->andWhere("date_format(InterruptDate,'%Y-%m-%d') = :fecha",[':fecha'=> date_format(new \DateTime($this->InterruptDate),'Y-m-d')]);
        }
        if(!empty($this->SolutionDate)){
            $query->andWhere("date_format(SolutionDate,'%Y-%m-%d') = :fecha",[':fecha'=> date_format(new \DateTime($this->SolutionDate),'Y-m-d')]);
        }
        if(!empty($this->Year)){
            $query->andWhere("date_format(TicketDate,'%Y') = :fecha",[':fecha'=> $this->Year ]);
        }

        $query->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'securityincident.Description', $this->Description]);

        return $dataProvider;
    }
}
