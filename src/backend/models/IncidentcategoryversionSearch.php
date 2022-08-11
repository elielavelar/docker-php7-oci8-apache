<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Incidentcategoryversion;

/**
 * IncidentcategoryversionSearch represents the model behind the search form of `backend\models\Incidentcategoryversion`.
 */
class IncidentcategoryversionSearch extends Incidentcategoryversion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'CurrentVersion', 'IdState'], 'integer'],
            [['Name', 'Version','Description','DateStart','DateEnd'], 'safe'],
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
        $query = Incidentcategoryversion::find();

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
            'CurrentVersion' => $this->CurrentVersion,
            'IdState' => $this->IdState,
        ]);
        
        if(!empty($this->DateStart)){
            $query->andWhere("date_format(DateStart,'%Y-%m-%d') = :datestart",[':datestart'=> date_format(new \DateTime($this->DateStart),'Y-m-d')]);
        }
        if(!empty($this->DateEnd)){
            $query->andWhere("date_format(DateEnd,'%Y-%m-%d') = :dateend",[':dateend'=> date_format(new \DateTime($this->DateEnd),'Y-m-d')]);
        }

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Version', $this->Version])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
