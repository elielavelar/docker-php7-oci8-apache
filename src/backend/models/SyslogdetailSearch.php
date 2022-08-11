<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Syslogdetail;

/**
 * SyslogdetailSearch represents the model behind the search form of `backend\models\Syslogdetail`.
 */
class SyslogdetailSearch extends Syslogdetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdSysLog'], 'integer'],
            [['Attribute', 'Value', 'OldValue'], 'safe'],
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
        $query = Syslogdetail::find();

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
            'IdSysLog' => $this->IdSysLog,
        ]);

        $query->andFilterWhere(['like', 'Attribute', $this->Attribute])
            ->andFilterWhere(['like', 'Value', $this->Value])
            ->andFilterWhere(['like', 'OldValue', $this->OldValue]);

        return $dataProvider;
    }
}
