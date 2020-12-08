<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Arquivos;

/**
 * ArquivosSearch represents the model behind the search form of `app\models\Arquivos`.
 */
class ArquivosSearch extends Arquivos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdArquivo', 'Diretorio', 'Nome', 'DataCadastro', 'Descricao'], 'safe'],
            [['IdUsuario'], 'integer'],
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
        $query = Arquivos::find();

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
            'IdUsuario' => $this->IdUsuario,
        ]);

        $query->andFilterWhere(['like', 'IdArquivo', $this->IdArquivo])
            ->andFilterWhere(['like', 'Diretorio', $this->Diretorio])
            ->andFilterWhere(['like', 'Descricao', $this->Descricao])
            ->andFilterWhere(['like', 'Nome', $this->Nome])
            ->andFilterWhere(['like', 'DataCadastro', $this->DataCadastro]);

        return $dataProvider;
    }
}
