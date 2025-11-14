<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int $totalBusinesses */

$this->title = 'Reportes';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-3"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-4">
            Descarga reportes con la información completa de tus aliados en formato Excel o PDF.
            Total de aliados registrados: <strong><?= $totalBusinesses ?></strong>.
        </p>
        <div class="d-flex flex-wrap gap-3">
            <?= Html::a('Descargar Excel', ['excel'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Descargar PDF', ['pdf'], ['class' => 'btn btn-outline-primary']) ?>
        </div>
    </div>
</div>

