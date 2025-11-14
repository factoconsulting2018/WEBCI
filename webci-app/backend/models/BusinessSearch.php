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
            [['name', 'slug', 'whatsapp', 'address', 'email'], 'safe'],
            [['show_on_home', 'is_active'], 'boolean'],
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

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['email_template_id' => $this->email_template_id]);
        $query->andFilterWhere(['show_on_home' => $this->show_on_home]);
        $query->andFilterWhere(['is_active' => $this->is_active]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'whatsapp', $this->whatsapp]);
        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}

