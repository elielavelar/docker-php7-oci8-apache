<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointmentrequirement;

/**
 * Description of AppointmentrequirementSearch
 *
 * @author AVELARE
 */
class AppointmentrequirementSearch extends Appointmentrequirement {
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdAppointment', 'IdCatalogdetailvalue', 'Enabled'], 'integer'],
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
        $query = Appointmentrequirement::find();

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
            'IdCatalogdetailvalue' => $this->IdCatalogdetailvalue,
            'Enabled' => $this->Enabled,
        ]);

        return $dataProvider;
    }
}
