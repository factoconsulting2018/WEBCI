<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var backend\models\BenefitSearch $benefitSearchModel */
/** @var yii\data\ActiveDataProvider $benefitDataProvider */
/** @var backend\models\BenefitCategorySearch $categorySearchModel */
/** @var yii\data\ActiveDataProvider $categoryDataProvider */
/** @var \common\models\Benefit $benefitForm */
/** @var \common\models\BenefitCategory $categoryForm */
/** @var array $categoryOptions */
/** @var array $logoOptions */
/** @var string $activeTab */

$this->title = 'Beneficios y categorías';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="benefits-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Administra el listado público de beneficios y sus categorías.</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'benefits' ? 'active' : '' ?>"
                    id="benefits-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#benefits-pane"
                    type="button"
                    role="tab"
                    aria-controls="benefits-pane"
                    aria-selected="<?= $activeTab === 'benefits' ? 'true' : 'false' ?>">
                Beneficios
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'categories' ? 'active' : '' ?>"
                    id="categories-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#categories-pane"
                    type="button"
                    role="tab"
                    aria-controls="categories-pane"
                    aria-selected="<?= $activeTab === 'categories' ? 'true' : 'false' ?>">
                Categorías
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade <?= $activeTab === 'benefits' ? 'show active' : '' ?>"
             id="benefits-pane"
             role="tabpanel"
             aria-labelledby="benefits-tab">
            <?= $this->render('_tab-benefits', [
                'benefitForm' => $benefitForm,
                'benefitDataProvider' => $benefitDataProvider,
                'benefitSearchModel' => $benefitSearchModel,
                'categoryOptions' => $categoryOptions,
                'logoOptions' => $logoOptions,
            ]) ?>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'categories' ? 'show active' : '' ?>"
             id="categories-pane"
             role="tabpanel"
             aria-labelledby="categories-tab">
            <?= $this->render('_tab-categories', [
                'categoryForm' => $categoryForm,
                'categoryDataProvider' => $categoryDataProvider,
                'categorySearchModel' => $categorySearchModel,
                'logoOptions' => $logoOptions,
            ]) ?>
        </div>
    </div>
</div>

