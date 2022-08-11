<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Incidentrequest;

/**
 * IncidentrequestSearch represents the model behind the search form of `backend\models\Incidentrequest`.
 */
class IncidentrequestSearch extends Incidentrequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdCategoryType', 'IdSubCategoryType', 'IdServiceCentre', 'IdReportUser', 'IdPriorityType', 'IdUser', 'IdApprovedUser', 'IdState', 'IdRejectUser'], 'integer'],
            [['RequestDate', 'IncidentDate', 'TokenId', 'Description', 'Code', 'RejectDate', 'ApprovedDate'], 'safe'],
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
        $query = Incidentrequest::find();

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
            'IdCategoryType' => $this->IdCategoryType,
            'IdSubCategoryType' => $this->IdSubCategoryType,
            'IdServiceCentre' => $this->IdServiceCentre,
            'IdReportUser' => $this->IdReportUser,
            'RequestDate' => $this->RequestDate,
            'IncidentDate' => $this->IncidentDate,
            'IdPriorityType' => $this->IdPriorityType,
            'IdUser' => $this->IdUser,
            'IdApprovedUser' => $this->IdApprovedUser,
            'IdState' => $this->IdState,
            'IdRejectUser' => $this->IdRejectUser,
            'RejectDate' => $this->RejectDate,
            'ApprovedDate' => $this->ApprovedDate,
        ]);

        $query->andFilterWhere(['like', 'TokenId', $this->TokenId])
            ->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'Code', $this->Code]);

        return $dataProvider;
    }
}
