<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Tramvigente;

/**
 * TramvigenteSearch represents the model behind the search form of `app\models\Tramvigente`.
 */
class TramvigenteSearch extends Tramvigente
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['NUM_SOLIC', 'NUM_AFIS', 'NUDUI', 'NUM_FOLIO_DUI', 'TPO_TRAM', 'NUM_REC_PAGO', 'COD_AGEN_BANCO', 'COD_BANCO', 'CONTEO_TRAM', 'ETAPA_TRAM', 'NUM_SOLIC_INV', 'NUM_AFIS_INV', 'PART_MARCADA', 'NUM_REC_PAGO2', 'COD_AGEN_BANCO2', 'COD_BANCO2'], 'integer'],
            [['dui'], 'safe'],
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
        $query = Tramvigente::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            #print_r("FALLO"); die();
            #$query->where('0=1');
            return $dataProvider;
        }
        
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(),[
                'dui' => [
                    'asc' => ['DUI' => SORT_ASC],
                    'desc' => ['DUI' => SORT_DESC],
                    'label' => 'DUI',
                    'default' => SORT_ASC
                ],
            ]),
        ]);
        
        $query->innerJoin('TRAM_VIGENTE_SDMS', 'TRAM_VIGENTE_SDMS.NUM_SOLIC = TRAM_VIGENTE.NUM_SOLIC');

        // grid filtering conditions
        $query->andFilterWhere([
            'TRAM_VIGENTE.NUM_SOLIC' => $this->NUM_SOLIC,
            'TRAM_VIGENTE.NUM_AFIS' => $this->NUM_AFIS,
            'TRAM_VIGENTE.NUDUI' => $this->NUDUI,
            'TRAM_VIGENTE.NUM_FOLIO_DUI' => $this->NUM_FOLIO_DUI,
            'TRAM_VIGENTE.TPO_TRAM' => $this->TPO_TRAM,
            'TRAM_VIGENTE_SDMS.DUI' => $this->dui,
        ]);
/*
        $query->andFilterWhere(['like', 'STAT_TRAM', $this->STAT_TRAM])
            ->andFilterWhere(['like', 'COD_MOTIVO_REPO', $this->COD_MOTIVO_REPO])
            ->andFilterWhere(['like', 'FEC_REGIS', $this->FEC_REGIS])
            ->andFilterWhere(['like', 'COD_OPER_REGIS', $this->COD_OPER_REGIS])
            ->andFilterWhere(['like', 'FEC_CAPT_IMG', $this->FEC_CAPT_IMG])
            ->andFilterWhere(['like', 'COD_OPER_IMG', $this->COD_OPER_IMG])
            ->andFilterWhere(['like', 'FEC_EMI_DUI', $this->FEC_EMI_DUI])
            ->andFilterWhere(['like', 'COD_OPER_EMI_DUI', $this->COD_OPER_EMI_DUI])
            ->andFilterWhere(['like', 'COD_IMPR_EMI_DUI', $this->COD_IMPR_EMI_DUI])
            ->andFilterWhere(['like', 'FEC_ENTR_DUI', $this->FEC_ENTR_DUI])
            ->andFilterWhere(['like', 'COD_OPER_ENTR_DUI', $this->COD_OPER_ENTR_DUI])
            ->andFilterWhere(['like', 'COD_CTRO_SERV', $this->COD_CTRO_SERV])
            ->andFilterWhere(['like', 'FEC_PAGO', $this->FEC_PAGO])
            ->andFilterWhere(['like', 'COD_SPRV_TRAM', $this->COD_SPRV_TRAM])
            ->andFilterWhere(['like', 'STAT_AFIS_AUTORIZA', $this->STAT_AFIS_AUTORIZA])
            ->andFilterWhere(['like', 'CND_HIT_REGIS', $this->CND_HIT_REGIS])
            ->andFilterWhere(['like', 'CND_HIT_CAPT_IMG', $this->CND_HIT_CAPT_IMG])
            ->andFilterWhere(['like', 'CND_HIT_VERIF', $this->CND_HIT_VERIF])
            ->andFilterWhere(['like', 'CND_HIT_ENTR', $this->CND_HIT_ENTR])
            ->andFilterWhere(['like', 'FEC_VENCE_DUI', $this->FEC_VENCE_DUI])
            ->andFilterWhere(['like', 'OBSERVACIONES', $this->OBSERVACIONES])
            ->andFilterWhere(['like', 'STAT_TRAM_TEMP', $this->STAT_TRAM_TEMP])
            ->andFilterWhere(['like', 'FEC_RECTIFICA', $this->FEC_RECTIFICA])
            ->andFilterWhere(['like', 'CONFIRMA', $this->CONFIRMA])
            ->andFilterWhere(['like', 'CND_HIT_REGIS_INV', $this->CND_HIT_REGIS_INV])
            ->andFilterWhere(['like', 'CND_HIT_VERIF_INV', $this->CND_HIT_VERIF_INV])
            ->andFilterWhere(['like', 'COD_CTRO_QUEDAN', $this->COD_CTRO_QUEDAN])
            ->andFilterWhere(['like', 'TPO_DOC_PRESENT', $this->TPO_DOC_PRESENT])
            ->andFilterWhere(['like', 'CONF_IMAGENES', $this->CONF_IMAGENES])
            ->andFilterWhere(['like', 'FEC_ENTR_RECTIFICA', $this->FEC_ENTR_RECTIFICA])
            ->andFilterWhere(['like', 'COD_OPER_COMPLE', $this->COD_OPER_COMPLE])
            ->andFilterWhere(['like', 'COD_SUPERV_COMPLE', $this->COD_SUPERV_COMPLE])
            ->andFilterWhere(['like', 'FEC_COMPLE', $this->FEC_COMPLE])
            ->andFilterWhere(['like', 'FEC_SUPERV_COMPLE', $this->FEC_SUPERV_COMPLE])
            ->andFilterWhere(['like', 'COD_OPER_RECTIFICA', $this->COD_OPER_RECTIFICA])
            ->andFilterWhere(['like', 'COD_DEL_RECTIFICA', $this->COD_DEL_RECTIFICA])
            ->andFilterWhere(['like', 'COD_OPER_REIMP', $this->COD_OPER_REIMP])
            ->andFilterWhere(['like', 'COD_DEL_REIMP', $this->COD_DEL_REIMP])
            ->andFilterWhere(['like', 'COD_DEL_CORREC', $this->COD_DEL_CORREC])
            ->andFilterWhere(['like', 'CALIDAD_IMAGEN', $this->CALIDAD_IMAGEN])
            ->andFilterWhere(['like', 'FEC_PAGO2', $this->FEC_PAGO2])
            ->andFilterWhere(['like', 'COD_CTRO_SERV_RECTI', $this->COD_CTRO_SERV_RECTI])
            ->andFilterWhere(['like', 'SERIE_RECIBO1', $this->SERIE_RECIBO1])
            ->andFilterWhere(['like', 'SERIE_RECIBO2', $this->SERIE_RECIBO2])
            ->andFilterWhere(['like', 'FEC_HORA_INICIO', $this->FEC_HORA_INICIO]);
        */

        return $dataProvider;
    }
}
