<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */

$this->title = 'Crear plantilla';
$this->params['breadcrumbs'][] = ['label' => 'Plantillas de email', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-4"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>

