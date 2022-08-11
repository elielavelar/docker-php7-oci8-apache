<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Incidentrequestdetail;

/**
 * IncidentrequestdetailSearch represents the model behind the search form of `backend\models\Incidentrequestdetail`.
 */
class IncidentrequestdetailSearch extends Incidentrequestdetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdIncidentRequest', 'IdActivityType', 'IdIncidentRequestState', 'IdUser', 'IdAssignedUser'], 'integer'],
            [['DetailDate', 'RecordDate', 'Description'], 'safe'],
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
        $query = Incidentrequestdetail::find();

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
            'IdIncidentRequest' => $this->IdIncidentRequest,
            'IdActivityType' => $this->IdActivityType,
            'DetailDate' => $this->DetailDate,
            'RecordDate' => $this->RecordDate,
            'IdIncidentRequestState' => $this->IdIncidentRequestState,
            'IdUser' => $this->IdUser,
            'IdAssignedUser' => $this->IdAssignedUser,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
