<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Securityincidentdetails;

/**
 * SecurityincidentdetailSearch represents the model behind the search form of `backend\models\Securityincidentdetails`.
 */
class SecurityincidentdetailSearch extends Securityincidentdetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdSecurityIncident', 'IdUser', 'IdActivityType', 'IdAssignedUser', 'IdIncidentState'], 'integer'],
            [['Title', 'Description', 'DetailDate', 'RecordDate', 'SolutionDate', 'Commentaries', 'Investigation', 'KnowledgeBase'], 'safe'],
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
        $query = Securityincidentdetails::find();

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
            'IdSecurityIncident' => $this->IdSecurityIncident,
            'DetailDate' => $this->DetailDate,
            'RecordDate' => $this->RecordDate,
            'SolutionDate' => $this->SolutionDate,
            'IdUser' => $this->IdUser,
            'IdActivityType' => $this->IdActivityType,
            'IdAssignedUser' => $this->IdAssignedUser,
            'IdIncidentState' => $this->IdIncidentState,
        ]);

        $query->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'Commentaries', $this->Commentaries])
            ->andFilterWhere(['like', 'Investigation', $this->Investigation])
            ->andFilterWhere(['like', 'KnowledgeBase', $this->KnowledgeBase]);

        return $dataProvider;
    }
}
