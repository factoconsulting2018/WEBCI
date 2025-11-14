<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Business $model */
/** @var array $categoryItems */
/** @var array $templateItems */

$this->title = 'Actualizar aliado: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Aliados', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-4"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', [
            'model' => $model,
            'categoryItems' => $categoryItems,
            'templateItems' => $templateItems,
        ]) ?>
    </div>
</div>

