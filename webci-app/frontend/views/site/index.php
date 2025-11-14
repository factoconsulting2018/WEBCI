<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \common\models\Business[] $businesses */
/** @var array $sponsors */
/** @var string $sort */

$this->title = 'La próxima generación de emails';
?>

<section class="hero-section surface-bright">
    <div class="container-wide hero-grid">
        <div class="hero-copy">
            <span class="chip chip-gradient">React Email Studio</span>
            <h1 class="hero-title">La próxima generación de emails increíbles</h1>
            <p class="hero-subtitle">
                Un set de componentes y herramientas que te permiten crear experiencias de correo impecables usando React, Tailwind y TypeScript.
            </p>
            <div class="hero-actions">
                <?= Html::a('Explorar componentes', '#componentes', ['class' => 'mdc-filled-button']) ?>
                <?= Html::a('Ver documentación', '#docs', ['class' => 'mdc-outlined-button']) ?>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-sphere hero-sphere-large"></div>
            <div class="hero-sphere hero-sphere-small"></div>
            <div class="hero-window">
                <div class="code-pane">
<pre><code class="code-block">import { Button } from '@react-email/components';

export const WelcomeEmail = () => (
  &lt;Layout&gt;
    &lt;EmailHero/&gt;
    &lt;Button href="/activar"&gt;
      Activar cuenta
    &lt;/Button&gt;
  &lt;/Layout&gt;
);</code></pre>
                </div>
                <div class="preview-pane">
                    <span class="preview-pill">Hola, Nicole</span>
                    <h3>Bienvenida a Helix</h3>
                    <p>Empieza a explorar tus nuevos componentes preferidos y publica campañas en minutos.</p>
                    <?= Html::a('Comenzar', '#componentes', ['class' => 'mdc-filled-button']) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="tools-section surface-container-low">
    <div class="container-wide tools-grid">
        <div class="tools-copy">
            <h2>Estiliza con cualquier herramienta</h2>
            <p>Crea componentes impecables con Tailwind, CSS inline o tu framework preferido.</p>
            <div class="pill-group">
                <span class="pill">Tailwind</span>
                <span class="pill">CSS</span>
            </div>
        </div>
        <div class="tools-preview">
            <div class="tools-card">
                <div class="tools-card-header">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <pre><code class="code-block small">import { Container, Heading, Text } from '@react-email/components';

&lt;Container className="bg-surface"&gt;
  &lt;Heading className="text-primary"&gt;
    Nuevos lanzamientos
  &lt;/Heading&gt;
  &lt;Text className="text-on-surface"&gt;
    Publica emails que se adaptan a todos los clientes con un solo código.
  &lt;/Text&gt;
&lt;/Container&gt;</code></pre>
            </div>
        </div>
    </div>
</section>

<section id="componentes" class="business-section container-wide">
    <header class="section-header">
        <h2 class="section-title">Aliados destacados</h2>
        <p class="section-subtitle">Comercios que confiaron en nuestra plataforma para despegar su presencia digital.</p>
        <div class="section-actions">
            <span class="sort-label">Ordenar por:</span>
            <?= Html::a('ID', ['site/index', 'sort' => 'id'], ['class' => 'sort-chip' . ($sort === 'id' ? ' active' : '')]) ?>
            <?= Html::a('Nombre', ['site/index', 'sort' => 'name'], ['class' => 'sort-chip' . ($sort === 'name' ? ' active' : '')]) ?>
        </div>
    </header>

    <div class="business-grid">
        <?php if (empty($businesses)): ?>
            <div class="empty-state surface-container">
                <h3>Aún no hay aliados publicados</h3>
                <p>Agrega comercios desde el panel administrativo para verlos aquí.</p>
            </div>
        <?php else: ?>
            <?php foreach ($businesses as $business): ?>
                <article class="business-card surface-container-high" data-business-id="<?= Html::encode($business->id) ?>">
                    <div class="card-header">
                        <div class="avatar">
                            <?= Html::img($business->getAvatarUrl(), ['alt' => $business->name]) ?>
                        </div>
                        <div>
                            <h3><?= Html::encode($business->name) ?></h3>
                            <p class="business-meta"><?= Html::encode($business->shortDescription) ?></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="info-list">
                            <li>
                                <span class="material-symbols-outlined">location_on</span>
                                <span><?= Html::encode($business->address) ?></span>
                            </li>
                            <li>
                                <span class="material-symbols-outlined">smartphone</span>
                                <span><?= Html::encode($business->whatsapp) ?></span>
                            </li>
                        </ul>
                        <div class="categories">
                            <?php foreach ($business->categories as $category): ?>
                                <span class="category-chip"><?= Html::encode($category->name) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="email-image">
                            <?= Html::img($business->getEmailImage(), [
                                'alt' => 'Correo de ' . $business->name,
                                'loading' => 'lazy',
                            ]) ?>
                        </div>
                        <div class="card-actions">
                            <?php if ($business->primarySocialUrl): ?>
                                <?= Html::a('Ver redes', $business->primarySocialUrl, ['class' => 'mdc-text-button', 'target' => '_blank', 'rel' => 'noopener']) ?>
                            <?php endif; ?>
                            <button class="mdc-filled-button open-contact-modal"
                                    data-business='<?= Html::encode(json_encode([
                                        'id' => $business->id,
                                        'name' => $business->name,
                                        'contactUrl' => $business->contactUrl,
                                    ])) ?>'>
                                Contactar
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="quote-section surface-container-high">
    <div class="container-narrow">
        <blockquote>
            “Construir UI de email es doloroso, especialmente cuando necesitas que se vea bien en todos los clientes. Nuestra plataforma lo hace sencillo.”
        </blockquote>
        <div class="quote-author">
            <div class="quote-avatar">LR</div>
            <div>
                <strong>Lee Robinson</strong>
                <span>VP Product, Helix</span>
            </div>
        </div>
    </div>
</section>

<section id="plantillas" class="components-section container-wide">
    <header class="section-header">
        <h2 class="section-title">Componentes listos para usar</h2>
        <p class="section-subtitle">Copiar, pegar, personalizar. Todo lo que necesitas para tu próxima campaña.</p>
        <?= Html::a('Ver todos', '#', ['class' => 'mdc-outlined-button']) ?>
    </header>
    <div class="component-grid">
        <div class="component-card surface-container">
            <h3>Notificaciones</h3>
            <span>5 componentes</span>
        </div>
        <div class="component-card surface-container">
            <h3>Facturas</h3>
            <span>3 componentes</span>
        </div>
        <div class="component-card surface-container">
            <h3>Onboarding</h3>
            <span>4 componentes</span>
        </div>
        <div class="component-card surface-container">
            <h3>Recibos</h3>
            <span>6 componentes</span>
        </div>
    </div>
</section>

<section class="clients-section surface-container-low" id="docs">
    <div class="container-wide">
        <h2>Probado en los clientes de correo más populares</h2>
        <div class="client-grid">
            <span class="client-chip">Gmail</span>
            <span class="client-chip">Apple Mail</span>
            <span class="client-chip">Outlook</span>
            <span class="client-chip">Yahoo</span>
            <span class="client-chip">HEY</span>
            <span class="client-chip">Superhuman</span>
        </div>
    </div>
</section>

<section class="tools-suite surface-container">
    <div class="container-wide suite-grid">
        <div class="suite-card">
            <h3>Linterna</h3>
            <p>Valida cada enlace del correo en segundos.</p>
        </div>
        <div class="suite-card">
            <h3>Checker de compatibilidad</h3>
            <p>Asegura que tu HTML funciona en los clientes más exigentes.</p>
        </div>
        <div class="suite-card">
            <h3>Score de spam</h3>
            <p>Mide tu contenido antes de que llegue a la bandeja de entrada.</p>
        </div>
    </div>
</section>

<section class="integrations-section surface-container-low">
    <div class="container-wide">
        <h2>Integra con cualquier servicio</h2>
        <div class="integration-grid">
            <span class="integration-chip">Resend</span>
            <span class="integration-chip">SendGrid</span>
            <span class="integration-chip">AWS</span>
            <span class="integration-chip">Postmark</span>
        </div>
    </div>
</section>

<section class="sponsors-section surface-container-high">
    <div class="container-wide">
        <h2>Patrocinadores</h2>
        <div class="sponsor-grid">
            <?php if (empty($sponsors)): ?>
                <div class="empty-state surface-container">
                    <p>Añade patrocinadores desde la administración para verlos aquí.</p>
                </div>
            <?php else: ?>
                <?php foreach ($sponsors as $sponsor): ?>
                    <div class="sponsor-card">
                        <?= Html::img($sponsor, ['alt' => 'Patrocinador', 'loading' => 'lazy']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<div id="contact-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="chip chip-gradient">Contacto comercial</span>
            <h3 id="contact-modal-title">Nombre del comercio</h3>
        </div>
        <form id="contact-modal-form" method="post">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <?= Html::hiddenInput('business_id', '', ['id' => 'contact-business-id']) ?>
            <div class="field">
                <label for="contact-fullname">Nombre completo</label>
                <input type="text" id="contact-fullname" name="fullname" required>
            </div>
            <div class="field">
                <label for="contact-phone">Teléfono</label>
                <input type="text" id="contact-phone" name="phone" required>
            </div>
            <div class="field">
                <label for="contact-address">Dirección</label>
                <input type="text" id="contact-address" name="address" required>
            </div>
            <div class="field">
                <label for="contact-subject">Asunto</label>
                <input type="text" id="contact-subject" name="subject" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="mdc-text-button modal-close">Cancelar</button>
                <button type="submit" class="mdc-filled-button">Enviar</button>
            </div>
        </form>
    </div>
</div>
 