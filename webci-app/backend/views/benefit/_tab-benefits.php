<?php

use common\services\LogoCatalog;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \common\models\Benefit $benefitForm */
/** @var yii\data\ActiveDataProvider $benefitDataProvider */
/** @var backend\models\BenefitSearch $benefitSearchModel */
/** @var array $categoryOptions */
/** @var array $logoOptions */
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 mb-1">
                            <?= $benefitForm->isNewRecord ? 'Crear beneficio' : 'Editar beneficio' ?>
                        </h2>
                        <p class="text-muted mb-0">Define el texto que verá el público en la landing.</p>
                    </div>
                    <?php if (!$benefitForm->isNewRecord): ?>
                        <?= Html::a('Nuevo', ['index', 'tab' => 'benefits'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>

                <?php if (empty($categoryOptions)): ?>
                    <div class="alert alert-warning mb-4">
                        Primero crea al menos una categoría para poder asociar los beneficios.
                    </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin([
                    'options' => ['data-pjax' => 0],
                ]); ?>
                <?= Html::hiddenInput('activeTab', 'benefits') ?>

                <?= $form->field($benefitForm, 'category_id')
                    ->dropDownList($categoryOptions, [
                        'prompt' => 'Selecciona una categoría',
                        'disabled' => empty($categoryOptions),
                    ]) ?>

                <?= $form->field($benefitForm, 'title')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Texto del beneficio']) ?>

                <?= $form->field($benefitForm, 'description')
                    ->textarea(['rows' => 3, 'placeholder' => 'Detalle opcional']) ?>

                <?= $form->field($benefitForm, 'logo')
                    ->dropDownList($logoOptions, ['prompt' => 'Selecciona un logo']) ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($benefitForm, 'sort_order')
                            ->input('number', ['min' => 0]) ?>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <?= $form->field($benefitForm, 'is_active')->checkbox() ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <div class="text-muted small">
                        <?= $benefitForm->isNewRecord ? 'Se enumerará automáticamente.' : 'Actualiza para guardar cambios.' ?>
                    </div>
                    <?= Html::submitButton(
                        $benefitForm->isNewRecord ? 'Guardar beneficio' : 'Actualizar beneficio',
                        ['class' => 'btn btn-primary', 'disabled' => empty($categoryOptions)]
                    ) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Listado de beneficios</h2>
                </div>

                <?php Pjax::begin(['id' => 'benefit-grid']); ?>
                <?= GridView::widget([
                    'dataProvider' => $benefitDataProvider,
                    'filterModel' => $benefitSearchModel,
                    'tableOptions' => ['class' => 'table table-striped align-middle'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'title',
                            'label' => 'Beneficio',
                            'format' => 'ntext',
                        ],
                        [
                            'attribute' => 'category_id',
                            'label' => 'Categoría',
                            'value' => static fn($model) => $model->category->name ?? 'Sin categoría',
                            'filter' => $categoryOptions,
                        ],
                        [
                            'attribute' => 'logo',
                            'value' => static fn($model) => LogoCatalog::getLabel($model->logo),
                            'filter' => $logoOptions,
                        ],
                        [
                            'attribute' => 'sort_order',
                            'label' => 'Orden',
                            'contentOptions' => ['style' => 'width:90px;'],
                        ],
                        [
                            'attribute' => 'is_active',
                            'format' => 'boolean',
                            'filter' => [1 => 'Activo', 0 => 'Inactivo'],
                            'contentOptions' => ['style' => 'width:110px;'],
                        ],
                        [
                            'label' => 'Acciones',
                            'format' => 'raw',
                            'value' => static function ($model) {
                                return Html::a('Editar', ['benefit/index', 'tab' => 'benefits', 'benefitId' => $model->id], ['class' => 'btn btn-sm btn-link'])
                                    . Html::a('Eliminar', ['benefit/delete-benefit', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-link text-danger',
                                        'data' => [
                                            'confirm' => '¿Seguro que deseas eliminar este beneficio?',
                                            'method' => 'post',
                                        ],
                                    ]);
                            },
                            'contentOptions' => ['style' => 'width:180px;'],
                        ],
                    ],
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>

