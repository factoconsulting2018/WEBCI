<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categorías';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Crear categoría', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        'slug',
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

<div class="pagination-wrapper">
    <?= \yii\widgets\LinkPager::widget([
        'pagination' => $dataProvider->pagination,
        'options' => ['class' => 'pagination justify-content-center custom-pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'activePageCssClass' => 'active',
        'disabledPageCssClass' => 'disabled',
        'pageCssClass' => 'page-item',
        'prevPageCssClass' => 'page-item',
        'nextPageCssClass' => 'page-item',
        'maxButtonCount' => 7,
    ]) ?>
</div>

