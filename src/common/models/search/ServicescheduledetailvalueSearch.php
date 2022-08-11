<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Servicescheduledetailvalue;

/**
 * ServicescheduledetailvalueSearch represents the model behind the search form of `common\models\Servicescheduledetailvalue`.
 */
class ServicescheduledetailvalueSearch extends Servicescheduledetailvalue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdScheduleDetail', 'IdDay', 'IdHour', 'Quantity', 'Enabled'], 'integer'],
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
        $query = Servicescheduledetailvalue::find();

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
            'IdScheduleDetail' => $this->IdScheduleDetail,
            'IdDay' => $this->IdDay,
            'IdHour' => $this->IdHour,
            'Quantity' => $this->Quantity,
            'Enabled' => $this->Enabled,
        ]);

        return $dataProvider;
    }
}
