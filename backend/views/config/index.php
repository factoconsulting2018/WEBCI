<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var \common\models\SiteConfig $model */

$this->title = 'Configuración';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-4"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <?= $form->field($model, 'logoFile')->fileInput()->hint('Formatos permitidos: PNG o JPG. Máximo 2MB.') ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'logo_width')->input('number', ['min' => 50, 'max' => 2000, 'placeholder' => 'Ej: 220']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'logo_height')->input('number', ['min' => 50, 'max' => 2000, 'placeholder' => 'Ej: 220'])->hint('Si se deja vacío, se mantendrá proporcional al ancho.') ?>
            </div>
        </div>

        <?php if ($model->logo_path): ?>
            <div class="mb-3">
                <label class="form-label d-block text-muted">Logo actual:</label>
                <?= Html::img($model->logo_path, [
                    'class' => 'img-fluid rounded',
                    'style' => 'max-height:180px',
                    'alt' => 'Logo actual',
                ]) ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Guardar cambios', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

