<?php

use Yii;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \common\models\Business[] $businesses */
/** @var array $sponsors */
/** @var string $sort */
/** @var \common\models\SiteConfig $siteConfig */

$logoUrl = $siteConfig && $siteConfig->logo_path ? $siteConfig->logo_path : null;
$defaultLogo = Yii::getAlias('@web/images/logo.png');
if (!$logoUrl && is_file(Yii::getAlias('@frontend/web/images/logo.png'))) {
    $logoUrl = $defaultLogo;
}
$logoWidth = $siteConfig && $siteConfig->logo_width ? (int)$siteConfig->logo_width : 150;
$logoHeight = $siteConfig && $siteConfig->logo_height ? (int)$siteConfig->logo_height : 150;
$logoStyle = [
    'width:' . $logoWidth . 'px',
    'height:' . $logoHeight . 'px',
    'object-fit:contain',
];

$this->title = 'La próxima generación de emails';
?>

<section class="hero-section surface-bright">
    <div class="container-wide hero-grid">
        <div class="hero-copy">
            <?php if ($logoUrl): ?>
                <div class="hero-logo">
                    <?= Html::img($logoUrl, [
                        'alt' => 'Logo Cámara',
                        'class' => 'hero-logo-img',
                        'style' => implode(';', $logoStyle),
                    ]) ?>
                </div>
            <?php endif; ?>
            <span class="hero-eyebrow">Bienvenidos</span>
            <h1 class="hero-title">Cámara Inversionistas de Costa Rica.</h1>
            <p class="hero-subtitle">
                Grupo empresarial de Costa Rica con más de 600 Aliados.
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
                <div class="preview-pane" style="grid-column: span 2;">
                    <span class="preview-pill">Hola, visitante</span>
                    <h3>¿Quiénes somos?</h3>
                    <p>
                        La Cámara de Inversionistas de Costa Rica somos una organización dedicada a apoyar y fortalecer a los emprendedores, pequeñas y medianas empresas (PYMEs), y empresarios en su crecimiento y éxito empresarial.
                    </p>
                    <p>
                        Creemos en el concepto de "capitalismo solidario", donde nuestros aliados no solo reciben asesoría y recursos, sino que también colaboran entre ellos, compartiendo conocimientos y creando redes de apoyo.
                    </p>
                    <?= Html::a('Comenzar', '#componentes', ['class' => 'mdc-filled-button']) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="tools-section surface-container-low">
    <div class="container-wide tools-grid">
        <div class="tools-copy">
            <h2>Nuestra Misión:</h2>
            <p>
                Proveer a nuestros aliados con acceso a asesorías estratégicas, servicios financieros, beneficios exclusivos, capacitación continua y oportunidades de networking, promoviendo el desarrollo sostenible y el éxito de cada negocio que forma parte de nuestra comunidad.
            </p>
            <div class="pill-group">
                <span class="pill">Tailwind</span>
                <span class="pill">CSS</span>
            </div>
        </div>
        <div class="tools-preview">
            <div class="tools-slider" id="tools-slider">
                <div class="slide active">
                    <div class="slide-header">
                        <span class="badge">Servicios</span>
                        <h3>Servicios Empresariales y Administrativos</h3>
                    </div>
                    <ul>
                        <li>Optimizamos su operación para que pueda concentrarse en crecer.</li>
                        <li>Servicio de facturación electrónica.</li>
                        <li>Asesoría completa para obtener el sello PYME del MEIC, con beneficios como la exención del IVA en alquileres.</li>
                        <li>Asesoría en el Registro de Marca para proteger su activo más valioso.</li>
                        <li>Acceso preferencial a nuestros servicios de oficina virtual y call center.</li>
                    </ul>
                </div>
                <div class="slide">
                    <div class="slide-header">
                        <span class="badge">Capacitación</span>
                        <h3>Capacitación y Desarrollo Profesional</h3>
                    </div>
                    <ul>
                        <li>El conocimiento es poder. Participe en talleres, ruedas de negocios, cursos y charlas estratégicas.</li>
                        <li>Ferias y capacitaciones diseñadas para su crecimiento.</li>
                        <li>Eventos de reconocimientos anuales a nuestra comunidad.</li>
                    </ul>
                </div>
                <div class="slide">
                    <div class="slide-header">
                        <span class="badge">Networking</span>
                        <h3>Comunidad y Oportunidades</h3>
                    </div>
                    <ul>
                        <li>Acceso a más de 600 aliados para alianzas estratégicas.</li>
                        <li>Mesas de negocio y ruedas de contacto.</li>
                        <li>Promoción constante en nuestros canales oficiales.</li>
                    </ul>
                </div>
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

    <div class="business-search surface-container">
        <input type="text"
               id="business-search"
               class="search-input"
               placeholder="Buscar aliados por nombre, categoría, teléfono, dirección o correo">
    </div>

    <div class="business-grid" id="business-grid">
        <?php if (empty($businesses)): ?>
            <div class="empty-state surface-container">
                <h3>Aún no hay aliados publicados</h3>
                <p>Agrega comercios desde el panel administrativo para verlos aquí.</p>
            </div>
        <?php else: ?>
            <?php foreach ($businesses as $business): ?>
                <?php
                    $searchBlob = strtoupper(trim(implode(' ', array_filter([
                        $business->name,
                        $business->summary,
                        $business->description,
                        $business->whatsapp,
                        $business->address,
                        $business->email,
                        implode(' ', array_map(static fn($category) => $category->name, $business->categories)),
                    ]))));
                ?>
                <?php
                    $detailPayload = [
                        'name' => $business->name,
                        'address' => $business->address,
                        'whatsapp' => $business->whatsapp,
                        'email' => $business->email,
                        'categories' => array_map(static fn($category) => $category->name, $business->categories),
                        'socialLinks' => array_values($business->getSocialLinks()),
                        'description' => strip_tags((string)$business->description),
                    ];
                ?>
                <article class="business-card surface-container-high"
                         data-business-id="<?= Html::encode($business->id) ?>"
                         data-search="<?= Html::encode($searchBlob) ?>">
                    <div class="card-header">
                        <div class="avatar">
                            <?= Html::img($business->getAvatarUrl(), ['alt' => $business->name]) ?>
                        </div>
                        <div>
                            <h3><?= Html::encode($business->name) ?></h3>
                            <?php if ($business->categories): ?>
                                <div class="business-category-line">
                                    <?php foreach ($business->categories as $index => $category): ?>
                                        <span><?= Html::encode($category->name) ?><?= $index < count($business->categories) - 1 ? ', ' : '' ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($business->summary) || !empty($business->description)): ?>
                            <p class="business-description">
                                <?= Html::encode($business->summary ?: strip_tags($business->description)) ?>
                            </p>
                        <?php endif; ?>
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
                    </div>
                    <div class="card-footer">
                        <div class="email-image">
                            <?= Html::img($business->getEmailImage(), [
                                'alt' => 'Correo de ' . $business->name,
                                'loading' => 'lazy',
                            ]) ?>
                        </div>
                        <div class="card-actions">
                            <button
                                type="button"
                                class="icon-button open-contact-modal"
                                title="Contactar aliado"
                                data-business='<?= Html::encode(json_encode([
                                    'id' => $business->id,
                                    'name' => $business->name,
                                    'contactUrl' => $business->contactUrl,
                                ])) ?>'>
                                <span class="material-symbols-outlined">mail</span>
                            </button>
                            <button
                                type="button"
                                class="icon-button open-share"
                                title="Compartir aliado"
                                data-share='<?= Html::encode(json_encode($detailPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>'>
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button
                                type="button"
                                class="info-button open-info-modal"
                                data-details='<?= Html::encode(json_encode($detailPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>'>
                                Ver información
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
            <strong>Nuestros Beneficios</strong><br><br>
            Un Ecosistema de Beneficios para su Empresa.<br><br>
            Al unirse a la Cámara de Inversionistas, obtiene acceso inmediato a un conjunto integral de herramientas diseñadas para impulsar su negocio.
        </blockquote>
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

<div id="info-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="hero-eyebrow">Ficha del aliado</span>
            <h3 id="info-modal-title">Aliado</h3>
        </div>
        <div class="info-modal-body">
            <div class="info-field">
                <span>Dirección</span>
                <p id="info-modal-address">Sin información</p>
            </div>
            <div class="info-field">
                <span>Teléfono</span>
                <p id="info-modal-phone">Sin información</p>
            </div>
            <div class="info-field">
                <span>Correo</span>
                <p id="info-modal-email">Sin información</p>
            </div>
            <div class="info-field">
                <span>Categorías</span>
                <ul id="info-modal-categories"></ul>
            </div>
            <div class="info-field">
                <span>Redes sociales</span>
                <ul id="info-modal-social"></ul>
            </div>
            <div class="info-field">
                <span>Descripción</span>
                <p id="info-modal-description">Sin información</p>
            </div>
        </div>
    </div>
</div>
 