<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Business $model */
/** @var array $categoryItems */
/** @var array $templateItems */
/** @var ActiveForm $form */
?>

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row">
    <div class="col-lg-8">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'summary')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'email_template_id')->dropDownList($templateItems, ['prompt' => 'Seleccione plantilla']) ?>
        <?= $form->field($model, 'show_on_home')->checkbox() ?>
        <?= $form->field($model, 'is_active')->checkbox() ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?= $form->field($model, 'categoryIds')->listBox($categoryItems, ['multiple' => true, 'size' => 8]) ?>

<?= $form->field($model, 'socialLinksInput')->textarea([
    'rows' => 4,
    'placeholder' => "Ejemplo:\nInstagram|https://instagram.com/mi-negocio\nFacebook|https://facebook.com/mi-negocio",
])->hint('Formato: NombreRed|https://url. Una red por línea.') ?>

<div class="row mb-3">
    <div class="col-lg-6">
        <?= $form->field($model, 'logoFile')->fileInput() ?>
    </div>
    <div class="col-lg-6">
        <?php if ($model->logo_path): ?>
            <div class="alert alert-secondary">
                <div class="mb-2">Logotipo actual:</div>
                <?= Html::img($model->logo_path, ['class' => 'img-fluid rounded', 'style' => 'max-height:120px']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Crear aliado' : 'Actualizar aliado', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

