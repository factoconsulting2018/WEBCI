<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\SponsorSet $model */

$this->title = 'Patrocinadores';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-4"><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'imageUploadOne')->fileInput() ?>
                <?php if ($model->image_one): ?>
                    <div class="border rounded p-2 bg-light text-center">
                        <?= Html::img($model->image_one, ['style' => 'max-width:100%;max-height:120px']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'imageUploadTwo')->fileInput() ?>
                <?php if ($model->image_two): ?>
                    <div class="border rounded p-2 bg-light text-center">
                        <?= Html::img($model->image_two, ['style' => 'max-width:100%;max-height:120px']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'imageUploadThree')->fileInput() ?>
                <?php if ($model->image_three): ?>
                    <div class="border rounded p-2 bg-light text-center">
                        <?= Html::img($model->image_three, ['style' => 'max-width:100%;max-height:120px']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'imageUploadFour')->fileInput() ?>
                <?php if ($model->image_four): ?>
                    <div class="border rounded p-2 bg-light text-center">
                        <?= Html::img($model->image_four, ['style' => 'max-width:100%;max-height:120px']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Guardar patrocinadores', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

