<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\EmailTemplateSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Plantillas de email';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Nueva plantilla', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        'subject',
        [
            'attribute' => 'is_default',
            'filter' => [1 => 'Sí', 0 => 'No'],
            'value' => static fn($model) => $model->is_default ? 'Sí' : 'No',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

