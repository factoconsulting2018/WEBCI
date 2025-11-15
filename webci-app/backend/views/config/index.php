<?php

use backend\models\SiteConfigForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var SiteConfigForm $model */
/** @var string|null $currentLogoPath */

$this->title = 'Configuración';
$this->params['breadcrumbs'][] = $this->title;

$logoPreview = null;
if ($currentLogoPath && is_file($currentLogoPath)) {
    $extension = pathinfo($currentLogoPath, PATHINFO_EXTENSION);
    $mime = $extension === 'png' ? 'image/png' : 'image/jpeg';
    $logoPreview = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($currentLogoPath));
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-4"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <?= $form->field($model, 'logoFile')->fileInput()->hint('Formatos permitidos: PNG o JPG. Máximo 4 MB.') ?>

        <div class="mb-4">
            <?php if ($logoPreview): ?>
                <p>Logo actual:</p>
                <img src="<?= $logoPreview ?>" alt="Logo actual" class="config-logo-preview">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Actualizar logo', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

