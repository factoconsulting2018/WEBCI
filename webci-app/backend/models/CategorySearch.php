<?php

namespace backend\models;

use common\models\Category;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CategorySearch extends Category
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'slug', 'description'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Category::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'slug', $this->slug]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}

