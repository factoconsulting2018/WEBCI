<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Html;

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
<body class="app-body">
<?php $this->beginBody() ?>

<div class="page-wrapper">
    <header class="site-header surface-container">
        <div class="container-wide header-bar">
            <div class="brand">
                <span class="brand-logo material-symbols-outlined">mail</span>
                <span class="brand-name"><?= Html::encode(Yii::$app->name) ?></span>
            </div>
            <nav class="main-nav">
                <?= Html::a('Componentes', '#componentes', ['class' => 'nav-link']) ?>
                <?= Html::a('Plantillas', '#plantillas', ['class' => 'nav-link']) ?>
                <?= Html::a('Docs', '#docs', ['class' => 'nav-link']) ?>
            </nav>
            <div class="header-actions">
                <span class="stat-pill">
                    <span class="dot"></span>
                    <span class="label">Versión 1.0</span>
                </span>
                <?= Html::a('Panel administrativo', Yii::$app->params['adminPanelUrl'] ?? '/admin', ['class' => 'mdc-tonal-button']) ?>
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container-wide flash-container">
            <?= Alert::widget() ?>
        </div>
        <?= $content ?>
    </main>

    <footer class="site-footer surface-container-low">
        <div class="container-wide footer-content">
            <div class="footer-brand">
                <span class="brand-logo material-symbols-outlined">mail</span>
                <span class="brand-name"><?= Html::encode(Yii::$app->name) ?></span>
            </div>
            <p class="footer-text">Impulsando el correo del futuro. &copy; <?= date('Y') ?></p>
            <div class="footer-links">
                <?= Html::a('Privacidad', '#', ['class' => 'nav-link']) ?>
                <?= Html::a('Términos', '#', ['class' => 'nav-link']) ?>
                <?= Html::a('Soporte', '#', ['class' => 'nav-link']) ?>
            </div>
        </div>
    </footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
