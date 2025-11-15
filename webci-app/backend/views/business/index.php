<?php

use yii\bootstrap5\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BusinessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Aliados';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h4 mb-0"><?= Html::encode($this->title) ?></h1>
    <div class="d-flex align-items-center gap-2">
        <?= Html::beginForm(['index'], 'get', ['data-pjax' => 1, 'class' => 'd-flex align-items-center gap-2']) ?>
            <?= Html::input('text', 'q', Yii::$app->request->get('q'), [
                'class' => 'form-control',
                'placeholder' => 'Buscar en todos los campos…',
                'style' => 'min-width:240px;',
            ]) ?>
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            <?php if (Yii::$app->request->get('q')): ?>
                <?= Html::a('Limpiar', ['index'], ['class' => 'btn btn-link text-danger']) ?>
            <?php endif; ?>
        <?= Html::endForm() ?>
        <?= Html::a('Crear aliado', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'layout' => "{items}\n<div class=\"pagination-wrapper d-flex justify-content-center\">{pager}</div>",
    'pager' => [
        'class' => LinkPager::class,
        'options' => ['class' => 'pagination custom-pagination'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledPageCssClass' => 'page-item disabled',
        'prevPageLabel' => '‹',
        'nextPageLabel' => '›',
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        [
            'attribute' => 'email',
            'format' => 'text',
            'value' => static fn($model) => $model->email ?: 'N/A',
        ],
        [
            'attribute' => 'whatsapp',
            'value' => static fn($model) => $model->whatsapp ?: 'N/A',
        ],
        [
            'attribute' => 'show_on_home',
            'value' => static fn($model) => $model->show_on_home ? 'Sí' : 'No',
        ],
        [
            'attribute' => 'available_in_search',
            'label' => 'Buscador',
            'value' => static fn($model) => $model->available_in_search ? 'Sí' : 'No',
        ],
        [
            'attribute' => 'is_active',
            'value' => static fn($model) => $model->is_active ? 'Activo' : 'Inactivo',
        ],
        [
            'label' => 'Categorías',
            'value' => static fn($model) => ($names = array_map(static fn($c) => $c->name, $model->categories)) ? implode(', ', $names) : 'N/A',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

