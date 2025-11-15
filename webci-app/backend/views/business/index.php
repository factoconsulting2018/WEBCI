<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BusinessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Aliados';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Crear aliado', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        [
            'attribute' => 'email',
            'format' => 'email',
        ],
        'whatsapp',
        [
            'attribute' => 'show_on_home',
            'value' => static fn($model) => $model->show_on_home ? 'Sí' : 'No',
        ],
        [
            'attribute' => 'is_active',
            'value' => static fn($model) => $model->is_active ? 'Activo' : 'Inactivo',
        ],
        [
            'label' => 'Categorías',
            'value' => static fn($model) => implode(', ', array_map(static fn($c) => $c->name, $model->categories)),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

