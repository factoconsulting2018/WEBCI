<?php

namespace backend\models;

use common\models\Benefit;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BenefitSearch extends Benefit
{
    public function rules(): array
    {
        return [
            [['id', 'category_id', 'sort_order'], 'integer'],
            [['title'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Benefit::find()->with('category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'id' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['category_id' => $this->category_id]);
        $query->andFilterWhere(['is_active' => $this->is_active]);
        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}

