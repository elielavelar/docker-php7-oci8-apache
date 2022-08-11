<?php

namespace client\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\prddui\Anexoacta;

/**
 * AnexoactaSearch represents the model behind the search form of `common\models\prddui\Anexoacta`.
 */
class AnexoactaSearch extends Anexoacta
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_CTRO_SERV', 'COD_JEFE', 'COD_DELEGADO', 'FEC_FACTURACION', 'FEC_ACTA'], 'safe'],
            [['NUM_CORR_ACTA', 'PRIMERAVEZ', 'MODIFICACIONES', 'REPOSICIONES', 'RENOVACIONES', 'REIMPRESIONES', 'TAR_BASE_ANULADAS', 'TAR_DECAD_ANULADAS'], 'number'],
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
        $query = Anexoacta::find();

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
            'NUM_CORR_ACTA' => $this->NUM_CORR_ACTA,
            'PRIMERAVEZ' => $this->PRIMERAVEZ,
            'MODIFICACIONES' => $this->MODIFICACIONES,
            'REPOSICIONES' => $this->REPOSICIONES,
            'RENOVACIONES' => $this->RENOVACIONES,
            'REIMPRESIONES' => $this->REIMPRESIONES,
            'TAR_BASE_ANULADAS' => $this->TAR_BASE_ANULADAS,
            'TAR_DECAD_ANULADAS' => $this->TAR_DECAD_ANULADAS,
        ]);

        $query->andFilterWhere(['like', 'COD_CTRO_SERV', $this->COD_CTRO_SERV])
            ->andFilterWhere(['like', 'COD_JEFE', $this->COD_JEFE])
            ->andFilterWhere(['like', 'COD_DELEGADO', $this->COD_DELEGADO])
            ->andFilterWhere(['like', 'FEC_FACTURACION', $this->FEC_FACTURACION])
            ->andFilterWhere(['like', 'FEC_ACTA', $this->FEC_ACTA]);

        return $dataProvider;
    }
}
