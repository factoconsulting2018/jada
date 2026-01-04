<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ParallaxBackground;

/**
 * ParallaxBackgroundSearch represents the model behind the search form of `app\models\ParallaxBackground`.
 */
class ParallaxBackgroundSearch extends ParallaxBackground
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'position'], 'integer'],
            [['section', 'image', 'title'], 'safe'],
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
        $query = ParallaxBackground::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['position' => SORT_ASC, 'created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'section', $this->section])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}


