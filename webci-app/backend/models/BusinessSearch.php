<?php

namespace backend\models;

use common\models\Business;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BusinessSearch extends Business
{
    public function rules(): array
    {
        return [
            [['id', 'email_template_id'], 'integer'],
            [['name', 'slug', 'whatsapp', 'address', 'email', 'summary', 'description', 'logo_path'], 'safe'],
            [['show_on_home', 'is_active', 'available_in_search'], 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Business::find()->with(['categories', 'emailTemplate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);
        $globalQuery = trim($params['q'] ?? '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['email_template_id' => $this->email_template_id]);
        $query->andFilterWhere(['show_on_home' => $this->show_on_home]);
        $query->andFilterWhere(['is_active' => $this->is_active]);
        $query->andFilterWhere(['available_in_search' => $this->available_in_search]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'whatsapp', $this->whatsapp]);
        $query->andFilterWhere(['like', 'address', $this->address]);
        $query->andFilterWhere(['like', 'summary', $this->summary]);
        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'slug', $this->slug]);

        if ($globalQuery !== '') {
            $query->andWhere([
                'or',
                ['like', 'name', $globalQuery],
                ['like', 'email', $globalQuery],
                ['like', 'whatsapp', $globalQuery],
                ['like', 'address', $globalQuery],
                ['like', 'summary', $globalQuery],
                ['like', 'description', $globalQuery],
                ['like', 'slug', $globalQuery],
            ]);
        }

        return $dataProvider;
    }
}

