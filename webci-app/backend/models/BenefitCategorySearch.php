<?php

namespace backend\models;

use common\models\BenefitCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BenefitCategorySearch extends BenefitCategory
{
    public function rules(): array
    {
        return [
            [['id', 'sort_order'], 'integer'],
            [['name'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = BenefitCategory::find();

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
        $query->andFilterWhere(['is_active' => $this->is_active]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}

