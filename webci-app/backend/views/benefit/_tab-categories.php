<?php

use common\services\LogoCatalog;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \common\models\BenefitCategory $categoryForm */
/** @var yii\data\ActiveDataProvider $categoryDataProvider */
/** @var backend\models\BenefitCategorySearch $categorySearchModel */
/** @var array $logoOptions */
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 mb-1">
                            <?= $categoryForm->isNewRecord ? 'Crear categoría' : 'Editar categoría' ?>
                        </h2>
                        <p class="text-muted mb-0">Estos títulos agrupan los beneficios en la web pública.</p>
                    </div>
                    <?php if (!$categoryForm->isNewRecord): ?>
                        <?= Html::a('Nuevo', ['index', 'tab' => 'categories'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>

                <?php $form = ActiveForm::begin([
                    'options' => ['data-pjax' => 0],
                ]); ?>
                <?= Html::hiddenInput('activeTab', 'categories') ?>

                <?= $form->field($categoryForm, 'name')
                    ->textInput(['maxlength' => true, 'placeholder' => 'Nombre de la categoría']) ?>

                <?= $form->field($categoryForm, 'description')
                    ->textarea(['rows' => 3, 'placeholder' => 'Descripción opcional']) ?>

                <?= $form->field($categoryForm, 'logo')
                    ->dropDownList($logoOptions, ['prompt' => 'Selecciona un logo']) ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($categoryForm, 'sort_order')->input('number', ['min' => 0]) ?>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <?= $form->field($categoryForm, 'is_active')->checkbox() ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <div class="text-muted small">
                        <?= $categoryForm->isNewRecord ? 'Será visible de inmediato si está activo.' : 'Actualiza para guardar cambios.' ?>
                    </div>
                    <?= Html::submitButton(
                        $categoryForm->isNewRecord ? 'Guardar categoría' : 'Actualizar categoría',
                        ['class' => 'btn btn-primary']
                    ) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h2 class="h5 mb-0">Listado de categorías</h2>
                    <?= Html::beginForm(['benefit/index'], 'get', [
                        'data-pjax' => 1,
                        'class' => 'd-flex align-items-center gap-2',
                    ]) ?>
                        <?= Html::hiddenInput('tab', 'categories') ?>
                        <?= Html::input('text', 'categoryQuery', Yii::$app->request->get('categoryQuery'), [
                            'class' => 'form-control',
                            'placeholder' => 'Buscar categorías…',
                            'style' => 'min-width:200px;',
                        ]) ?>
                        <button type="submit" class="btn btn-outline-primary btn-sm">Buscar</button>
                        <?php if (Yii::$app->request->get('categoryQuery')): ?>
                            <?= Html::a('Limpiar', ['benefit/index', 'tab' => 'categories'], ['class' => 'btn btn-link text-danger btn-sm']) ?>
                        <?php endif; ?>
                    <?= Html::endForm() ?>
                </div>

                <?php Pjax::begin(['id' => 'category-grid']); ?>
                <?= GridView::widget([
                    'dataProvider' => $categoryDataProvider,
                    'filterModel' => null,
                    'tableOptions' => ['class' => 'table table-striped align-middle'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'name',
                            'label' => 'Categoría',
                        ],
                        [
                            'attribute' => 'description',
                            'format' => 'ntext',
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
                                return Html::a('Editar', ['benefit/index', 'tab' => 'categories', 'categoryId' => $model->id], ['class' => 'btn btn-sm btn-link'])
                                    . Html::a('Eliminar', ['benefit/delete-category', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-link text-danger',
                                        'data' => [
                                            'confirm' => '¿Deseas eliminar esta categoría?',
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

