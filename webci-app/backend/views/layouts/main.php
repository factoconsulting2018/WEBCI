<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="admin-body">
<?php $this->beginBody() ?>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">
                <span class="material-symbols-outlined">space_dashboard</span>
            </div>
            <div>
                <span class="brand-title"><?= Html::encode(Yii::$app->name) ?></span>
                <span class="brand-subtitle">Panel administrativo</span>
            </div>
        </div>

        <?php
        $menuItems = [
            ['label' => 'Aliados', 'url' => ['/business/index']],
            ['label' => 'Categorías', 'url' => ['/category/index']],
            ['label' => 'Patrocinadores', 'url' => ['/sponsor/index']],
            ['label' => 'Plantillas de email', 'url' => ['/email-template/index']],
            ['label' => 'Reportes', 'url' => ['/report/index']],
            ['label' => 'Configuración', 'url' => ['/config/index']],
        ];

        echo Nav::widget([
            'options' => ['class' => 'nav flex-column admin-menu'],
            'items' => $menuItems,
            'encodeLabels' => false,
        ]);
        ?>

        <div class="sidebar-footer">
            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="sidebar-user">
                    <span class="material-symbols-outlined">account_circle</span>
                    <div>
                        <strong><?= Html::encode(Yii::$app->user->identity->username) ?></strong>
                        <span>Administrador</span>
                    </div>
                </div>
                <?= Html::beginForm(['/site/logout'], 'post') ?>
                <?= Html::submitButton('Cerrar sesión', ['class' => 'sidebar-logout']) ?>
                <?= Html::endForm() ?>
            <?php else: ?>
                <?= Html::a('Iniciar sesión', ['/site/login'], ['class' => 'sidebar-logout']) ?>
            <?php endif; ?>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <div>
                <h1 class="admin-title"><?= Html::encode($this->title) ?></h1>
                <p class="admin-subtitle">Gestiona el directorio y sus contenidos.</p>
            </div>
        </header>

        <main class="admin-content">
            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'] ?? [],
                'options' => ['class' => 'admin-breadcrumbs'],
                'homeLink' => ['label' => 'Inicio', 'url' => ['/business/index']],
            ]) ?>
            <?= Alert::widget() ?>
            <div class="admin-card">
                <?= $content ?>
            </div>
        </main>

        <footer class="admin-footer">
            <span>&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></span>
            <span>Powered by Ing. Ronald Rojas Castro | 8878-1108</span>
        </footer>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
