<?php

namespace backend\models;

use common\models\ContactSubmission;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ContactSubmissionSearch extends ContactSubmission
{
    public $businessName;

    public function rules(): array
    {
        return [
            [['id', 'business_id'], 'integer'],
            [['fullname', 'phone', 'address', 'subject', 'businessName'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ContactSubmission::find()->joinWith('business');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
                'attributes' => [
                    'id',
                    'fullname',
                    'phone',
                    'address',
                    'subject',
                    'created_at',
                    'businessName' => [
                        'asc' => ['business.name' => SORT_ASC],
                        'desc' => ['business.name' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => ['pageSize' => 25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['contact_submission.id' => $this->id]);
        $query->andFilterWhere(['contact_submission.business_id' => $this->business_id]);
        $query->andFilterWhere(['like', 'contact_submission.fullname', $this->fullname]);
        $query->andFilterWhere(['like', 'contact_submission.phone', $this->phone]);
        $query->andFilterWhere(['like', 'contact_submission.address', $this->address]);
        $query->andFilterWhere(['like', 'contact_submission.subject', $this->subject]);
        $query->andFilterWhere(['like', 'business.name', $this->businessName]);

        return $dataProvider;
    }
}

