<?php

use Yii;
use common\services\LogoCatalog;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \common\models\Business[] $businesses */
/** @var array $sponsors */
/** @var string $sort */
/** @var \common\models\SiteConfig $siteConfig */
/** @var \common\models\BenefitCategory[] $benefitCategories */
/** @var int[] $featuredBusinessIds */

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

$featuredLookup = array_fill_keys($featuredBusinessIds, true);
$businessViewData = [];
foreach ($businesses as $business) {
    $categoryNames = array_map(static fn($category) => $category->name, $business->categories);
    $categoryLine = $categoryNames ? implode(', ', $categoryNames) : 'N/A';
    $whatsappDisplay = $business->whatsapp ?: 'N/A';
    $emailDisplay = $business->email ?: 'N/A';
    $businessViewData[$business->id] = [
        'categoryNames' => $categoryNames,
        'categoryLine' => $categoryLine,
        'whatsapp' => $whatsappDisplay,
        'email' => $emailDisplay,
        'detailPayload' => [
            'name' => $business->name,
            'address' => $business->address,
            'whatsapp' => $whatsappDisplay,
            'email' => $emailDisplay,
            'categories' => $categoryNames,
            'socialLinks' => array_values($business->getSocialLinks()),
            'description' => strip_tags((string)$business->description),
        ],
    ];
}

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
            <span class="hero-eyebrow">Cámara Inversionistas</span>
            <h1 class="hero-title">Cámara Inversionistas de Costa Rica.</h1>
            <p class="hero-subtitle">
                Grupo empresarial de Costa Rica con más de 600 Aliados.
            </p>
            <div class="hero-actions">
                <?= Html::a('Directorio Comercial', '#componentes', ['class' => 'mdc-filled-button']) ?>
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

<?php if (!empty($businesses)): ?>
<section class="ally-heading-section surface-container-low">
    <div class="container-wide">
        <h1 class="ally-heading">Empresas que confían en nosotros</h1>
    </div>
</section>
<section class="ally-ticker-section surface-container">
    <div class="container-wide">
        <div class="ally-ticker" id="ally-ticker">
            <div class="ally-ticker-track" id="ally-ticker-track">
                <?php foreach ($businesses as $business): ?>
                    <?php $viewData = $businessViewData[$business->id]; ?>
                    <?php $detailJson = Html::encode(json_encode($viewData['detailPayload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>
                    <button type="button"
                            class="ticker-item"
                            data-details="<?= $detailJson ?>">
                        <span class="ticker-logo">
                            <?= Html::img($business->getAvatarUrl(), ['alt' => $business->name]) ?>
                        </span>
                        <span class="ticker-name"><?= Html::encode($business->name) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

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
        <?php if (empty($featuredBusinessIds)): ?>
            <div class="empty-state surface-container">
                <h3>Aún no hay aliados publicados</h3>
                <p>Utiliza el buscador para explorar los aliados disponibles.</p>
            </div>
        <?php endif; ?>
        <?php foreach ($businesses as $business): ?>
            <?php
                $viewData = $businessViewData[$business->id];
                $searchBlob = strtoupper(trim(implode(' ', array_filter([
                    $business->name,
                    $business->summary,
                    $business->description,
                    $viewData['whatsapp'],
                    $business->address,
                    $viewData['email'],
                    implode(' ', $viewData['categoryNames']),
                ]))));
                $detailJson = Html::encode(json_encode($viewData['detailPayload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $isFeatured = isset($featuredLookup[$business->id]);
            ?>
            <article class="business-card surface-container-high<?= $isFeatured ? '' : ' hidden-by-default' ?>"
                     data-business-id="<?= Html::encode($business->id) ?>"
                     data-search="<?= Html::encode($searchBlob) ?>"
                     data-featured="<?= $isFeatured ? '1' : '0' ?>">
                <div class="card-header">
                    <div class="avatar">
                        <?= Html::img($business->getAvatarUrl(), ['alt' => $business->name]) ?>
                    </div>
                    <div>
                        <h3><?= Html::encode($business->name) ?></h3>
                        <div class="business-category-line">
                            <span><?= Html::encode($viewData['categoryLine']) ?></span>
                        </div>
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
                            <span><?= Html::encode($viewData['whatsapp']) ?></span>
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
                        <div class="icon-actions">
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
                            data-share='<?= $detailJson ?>'>
                            <span class="material-symbols-outlined">share</span>
                        </button>
                        </div>
                        <button
                            type="button"
                            class="info-button open-info-modal"
                            data-details='<?= $detailJson ?>'>
                            Ver información
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php
$maxLocationValue = $locationStats ? max(array_column($locationStats, 'total')) : 1;
$distributionTotal = $categoryDistribution ? array_sum(array_column($categoryDistribution, 'total')) : 1;
?>

<section class="insights-section surface-container-high">
    <div class="container-wide insights-grid">
        <div class="insight-card highlight">
            <span class="insight-label">Aliados registrados</span>
            <h3><?= number_format($alliesCount) ?></h3>
            <p>Empresas activas dentro del directorio comercial.</p>
        </div>
        <div class="insight-card">
            <div class="insight-head">
                <h4>Presencia por ubicación</h4>
                <span class="insight-subtitle">Top 5 ubicaciones</span>
            </div>
            <ul class="stat-list">
                <?php if ($locationStats): ?>
                    <?php foreach (array_slice($locationStats, 0, 5) as $row): ?>
                        <?php $percent = $maxLocationValue ? round(($row['total'] / $maxLocationValue) * 100) : 0; ?>
                        <li>
                            <div class="stat-info">
                                <strong><?= Html::encode($row['location'] ?: 'Sin dirección') ?></strong>
                                <div class="stat-bar">
                                    <span style="width: <?= $percent ?>%"></span>
                                </div>
                            </div>
                            <span class="stat-value"><?= $row['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="stat-empty">No hay datos suficientes.</li>
                <?php endif; ?>
            </ul>
            <?= Html::a('Descargar PDF', ['site/location-report'], [
                'class' => 'mini-button',
                'target' => '_blank',
                'rel' => 'noopener',
            ]) ?>
        </div>
        <div class="insight-card">
            <div class="insight-head">
                <h4>Categorías más buscadas</h4>
                <span class="insight-subtitle">Ranking TOP</span>
            </div>
            <ol class="ranking-list">
                <?php if ($categoryRanking): ?>
                    <?php foreach ($categoryRanking as $index => $category): ?>
                        <li>
                            <span class="rank-number"><?= $index + 1 ?></span>
                            <div>
                                <strong><?= Html::encode($category['category_name']) ?></strong>
                                <span><?= $category['total'] ?> aliados</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="stat-empty">Sin registros.</li>
                <?php endif; ?>
            </ol>
            <button type="button" class="mini-button open-dashboard-modal" data-target="#ranking-modal">Ver detalles</button>
        </div>
        <div class="insight-card">
            <div class="insight-head">
                <h4>Distribución por categoría</h4>
                <span class="insight-subtitle">Participación porcentual</span>
            </div>
            <ul class="stat-list compact">
                <?php if ($categoryDistribution): ?>
                    <?php foreach ($categoryDistribution as $category): ?>
                        <?php $percent = $distributionTotal ? round(($category['total'] / $distributionTotal) * 100) : 0; ?>
                        <li>
                            <div class="stat-info">
                                <strong><?= Html::encode($category['category_name']) ?></strong>
                                <div class="stat-bar">
                                    <span style="width: <?= $percent ?>%"></span>
                                </div>
                            </div>
                            <span class="stat-value"><?= $percent ?>%</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="stat-empty">Sin datos aún.</li>
                <?php endif; ?>
            </ul>
            <button type="button" class="mini-button open-dashboard-modal" data-target="#distribution-modal">Ver detalles</button>
        </div>
    </div>
</section>

<div id="ranking-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="hero-eyebrow">Ranking</span>
            <h3>Categorías más buscadas</h3>
        </div>
        <div class="info-modal-body">
            <ol class="ranking-list">
                <?php foreach ($categoryRanking as $index => $category): ?>
                    <li>
                        <span class="rank-number"><?= $index + 1 ?></span>
                        <div>
                            <strong><?= Html::encode($category['category_name']) ?></strong>
                            <span><?= $category['total'] ?> aliados</span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>

<div id="distribution-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="hero-eyebrow">Distribución</span>
            <h3>Aliados por categoría</h3>
        </div>
        <div class="info-modal-body">
            <ul class="stat-list compact">
                <?php foreach ($categoryDistribution as $category): ?>
                    <li>
                        <div class="stat-info">
                            <strong><?= Html::encode($category['category_name']) ?></strong>
                        </div>
                        <span class="stat-value"><?= $category['total'] ?> aliados</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<section id="beneficios" class="benefits-section surface-container-high">
    <div class="container-wide">
        <header class="section-header simple">
            <div>
                <h2 class="section-title">Listado de Beneficios</h2>
                <h3 class="section-subtitle">Cámara de Inversionistas de Costa Rica</h3>
            </div>
            <div class="section-actions">
                <?= Html::a('Descargar Listado de Beneficios', ['site/benefit-report'], [
                    'class' => 'benefit-download-button',
                    'target' => '_blank',
                    'rel' => 'noopener',
                ]) ?>
            </div>
        </header>

        <?php if (!empty($benefitCategories)): ?>
            <?php $benefitCounter = 1; ?>
            <div class="benefits-ledger">
                <?php foreach ($benefitCategories as $category): ?>
                    <?php
                    $categoryLogo = LogoCatalog::getUrl($category->logo);
                    $hasBenefits = !empty($category->benefits);
                    ?>
                    <div class="benefits-category surface-container">
                        <div class="category-header">
                            <?php if ($categoryLogo): ?>
                                <div class="category-logo">
                                    <?= Html::img($categoryLogo, [
                                        'alt' => 'Icono ' . ($category->name ?? ''),
                                        'loading' => 'lazy',
                                    ]) ?>
                                </div>
                            <?php endif; ?>
                            <div class="category-copy">
                                <h3><?= Html::encode($category->name) ?></h3>
                                <?php if ($category->description): ?>
                                    <p><?= Html::encode($category->description) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($hasBenefits): ?>
                        <?php foreach ($category->benefits as $benefit): ?>
                            <?php
                            $benefitLogo = LogoCatalog::getUrl($benefit->logo) ?: $categoryLogo;
                            ?>
                            <div
                                class="benefit-row surface-container"
                                role="button"
                                tabindex="0"
                                aria-label="Solicitar información sobre <?= Html::encode($benefit->title) ?>"
                                data-benefit-title="<?= Html::encode($benefit->title) ?>"
                                data-category-name="<?= Html::encode($category->name) ?>"
                            >
                                <span class="benefit-index"><?= str_pad((string)$benefitCounter++, 2, '0', STR_PAD_LEFT) ?></span>
                            <div class="benefit-copy">
                                    <h4><?= Html::encode($benefit->title) ?></h4>
                                    <?php if ($benefit->description): ?>
                                        <p><?= nl2br(Html::encode($benefit->description)) ?></p>
                                    <?php endif; ?>
                            </div>
                                <div class="benefit-cta">
                                    <?php if ($benefitLogo): ?>
                                        <div class="benefit-logo">
                                            <?= Html::img($benefitLogo, [
                                                'alt' => 'Logo beneficio',
                                                'loading' => 'lazy',
                                            ]) ?>
                                        </div>
                                    <?php endif; ?>
                                    <button type="button" class="benefit-action">Haz clic para solicitar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="benefit-row surface-container empty">
                            <div class="benefit-copy">
                                <p>Aún no hay beneficios registrados para esta categoría.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state surface-container">
                <p>Gestiona los beneficios desde la administración para mostrarlos aquí.</p>
            </div>
        <?php endif; ?>
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

<div id="benefit-modal" class="contact-modal" data-state="closed">
    <div class="modal-overlay"></div>
    <div class="modal-card surface-container-high">
        <button class="modal-close material-symbols-outlined" type="button">close</button>
        <div class="modal-header">
            <span class="chip chip-gradient">Beneficios</span>
            <h3 id="benefit-modal-title">Beneficio</h3>
        </div>
        <div class="modal-tabs">
            <button class="modal-tab-button active" type="button" data-tab="benefit-tab-consult">Hacer consulta sobre el beneficio</button>
            <button class="modal-tab-button" type="button" data-tab="benefit-tab-register">Registro online</button>
        </div>
        <div class="modal-tab-content active" id="benefit-tab-consult">
            <?= Html::beginForm(['/site/benefit-inquiry'], 'post', ['class' => 'benefit-form', 'id' => 'benefit-consult-form']) ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <?= Html::hiddenInput('type', 'consult') ?>
            <?= Html::hiddenInput('benefit_title', '', ['class' => 'benefit-title-input']) ?>
            <div class="field">
                <label for="benefit-consult-name">Nombre</label>
                <input type="text" id="benefit-consult-name" name="name" required>
            </div>
            <div class="field">
                <label for="benefit-consult-phone">Teléfono</label>
                <input type="text" id="benefit-consult-phone" name="phone" required>
            </div>
            <div class="field">
                <label for="benefit-consult-medium">Medio de contacto</label>
                <select id="benefit-consult-medium" name="contact_medium" required>
                    <option value="">Selecciona una opción</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
            </div>
            <div class="field">
                <label for="benefit-consult-subject">Consulta</label>
                <textarea id="benefit-consult-subject" name="subject" rows="4" maxlength="500" placeholder="Escribe tu consulta" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="mdc-filled-button">Enviar consulta</button>
            </div>
            <?= Html::endForm() ?>
        </div>
        <div class="modal-tab-content" id="benefit-tab-register">
            <?= Html::beginForm(['/site/benefit-inquiry'], 'post', ['class' => 'benefit-form', 'id' => 'benefit-register-form']) ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <?= Html::hiddenInput('type', 'register') ?>
            <?= Html::hiddenInput('benefit_title', '', ['class' => 'benefit-title-input']) ?>
            <div class="field span-1">
                <label for="benefit-register-name">Nombre</label>
                <input type="text" id="benefit-register-name" name="name" required>
            </div>
            <div class="field span-1">
                <label for="benefit-register-phone">Teléfono</label>
                <input type="text" id="benefit-register-phone" name="phone" required>
            </div>
            <div class="field span-1">
                <label for="benefit-register-type">Tipo de comercio</label>
                <input type="text" id="benefit-register-type" name="business_type" required>
            </div>
            <div class="field span-1">
                <label for="benefit-register-trade">Nombre comercial (opcional)</label>
                <input type="text" id="benefit-register-trade" name="business_name">
            </div>
            <div class="field span-1">
                <label for="benefit-register-email">Email</label>
                <input type="email" id="benefit-register-email" name="email" required>
            </div>
            <div class="field span-1">
                <label for="benefit-register-address">Dirección física</label>
                <input type="text" id="benefit-register-address" name="address" required>
            </div>
            <div class="field span-2">
                <label>Patentado</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="patentado" value="si" required>
                        Sí
                    </label>
                    <label>
                        <input type="radio" name="patentado" value="no" required>
                        No
                    </label>
                </div>
            </div>
            <div class="field span-2">
                <label for="benefit-register-subject">Asunto</label>
                <textarea id="benefit-register-subject" name="subject" rows="4" maxlength="500" placeholder="Describe tu solicitud (máx. 500 caracteres)" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="mdc-filled-button">Enviar registro</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

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
 