<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointmentattachment;

/**
 * AppointmentattachmentSearch represents the model behind the search form of `common\models\Appointmentattachment`.
 */
class AppointmentattachmentSearch extends Appointmentattachment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdAppointment', 'IdClientUser', 'IdCatalogDetail'], 'integer'],
            [['FileName', 'FileExtension', 'Description'], 'safe'],
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
        $query = Appointmentattachment::find();

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
            'IdAppointment' => $this->IdAppointment,
            'IdClientUser' => $this->IdClientUser,
            'IdCatalogDetail' => $this->IdCatalogDetail,
        ]);

        $query->andFilterWhere(['like', 'FileName', $this->FileName])
            ->andFilterWhere(['like', 'FileExtension', $this->FileExtension])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
