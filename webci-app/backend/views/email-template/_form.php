<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */
/** @var ActiveForm $form */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'is_default')->checkbox()->hint('Solo una plantilla puede ser predeterminada.') ?>

<?= $form->field($model, 'html_body')->textarea([
    'rows' => 10,
    'class' => 'form-control font-monospace',
])->hint('Variables disponibles: {{businessName}}, {{fullName}}, {{phone}}, {{address}}, {{subject}}') ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Crear plantilla' : 'Guardar cambios', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

