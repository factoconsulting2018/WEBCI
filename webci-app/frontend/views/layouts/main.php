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
                <?= Html::a('Directorio Comercial', '#componentes', ['class' => 'nav-link']) ?>
                <?= Html::a('Beneficios', '#beneficios', ['class' => 'nav-link']) ?>
                <?= Html::a('Contacto', '#', ['class' => 'nav-link open-main-contact']) ?>
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
            <p class="footer-text">Diseño a cargo. Ing. Ronald Rojas Castro | FactoSystems.</p>
            <div class="footer-links">
                <?= Html::a('Privacidad', '#', ['class' => 'nav-link']) ?>
                <?= Html::a('Términos', '#', ['class' => 'nav-link']) ?>
                <?= Html::a('Soporte', '#', ['class' => 'nav-link']) ?>
            </div>
        </div>
    </footer>
</div>

<div id="main-contact-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="hero-eyebrow">Contáctenos</span>
            <h3>Centro de atención</h3>
        </div>
        <div class="info-modal-body">
            <div class="info-field">
                <span>Central telefónica</span>
                <p>4070-0485</p>
            </div>
            <div class="info-field">
                <span>Correo</span>
                <p>info@camarainversionistas.com</p>
            </div>
            <div class="info-field">
                <span>Sinpe Móvil</span>
                <p>8970-7805</p>
            </div>
        </div>
        <form id="main-contact-form" class="main-contact-form">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <div class="field">
                <label for="main-contact-name">Nombre completo</label>
                <input type="text" id="main-contact-name" name="fullname" required>
            </div>
            <div class="field">
                <label for="main-contact-phone">Teléfono</label>
                <input type="text" id="main-contact-phone" name="phone" required>
            </div>
            <div class="field">
                <label for="main-contact-email">Email</label>
                <input type="email" id="main-contact-email" name="email" required>
            </div>
            <div class="field">
                <label for="main-contact-subject">Asunto</label>
                <input type="text" id="main-contact-subject" name="subject" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="mdc-text-button modal-close">Cancelar</button>
                <button type="submit" class="mdc-filled-button">Enviar</button>
            </div>
        </form>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
