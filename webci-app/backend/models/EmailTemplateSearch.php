<?php

namespace backend\models;

use common\models\EmailTemplate;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class EmailTemplateSearch extends EmailTemplate
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'subject'], 'safe'],
            [['is_default'], 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = EmailTemplate::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['is_default' => SORT_DESC, 'id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['is_default' => $this->is_default]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'subject', $this->subject]);

        return $dataProvider;
    }
}

