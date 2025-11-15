<?php

namespace frontend\controllers;

use common\models\BenefitCategory;
use common\models\Business;
use common\models\SiteConfig;
use common\models\SponsorSet;
use common\services\LogoCatalog;
use frontend\models\ContactBusinessForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'contact-business' => ['post'],
                    'contact-general' => ['post'],
                ],
            ],
        ];
    }

    public function actionContactGeneral()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Solicitud inválida.');
        }

        $fullname = trim($request->post('fullname', ''));
        $phone = trim($request->post('phone', ''));
        $email = trim($request->post('email', ''));
        $subject = trim($request->post('subject', ''));

        if ($fullname === '' || $phone === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '') {
            return $this->asJson([
                'success' => false,
                'message' => 'Completa todos los campos correctamente.',
            ]);
        }

        $sent = Yii::$app->mailer->compose()
            ->setTo('info@camarainversionistas.com')
            ->setSubject('Contacto general: ' . $subject)
            ->setTextBody("Nombre: {$fullname}\nTeléfono: {$phone}\nEmail: {$email}\nAsunto: {$subject}")
            ->setHtmlBody("
                <p><strong>Nombre:</strong> {$fullname}</p>
                <p><strong>Teléfono:</strong> {$phone}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Asunto:</strong><br>{$subject}</p>
            ")
            ->send();

        return $this->asJson([
            'success' => (bool)$sent,
            'message' => $sent ? 'Tu mensaje fue enviado.' : 'No se pudo enviar el mensaje.',
        ]);
    }

    public function actionBenefitInquiry()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Solicitud inválida.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $type = $request->post('type', '');
        $benefitTitle = trim($request->post('benefit_title', ''));
        $benefitTitle = $benefitTitle !== '' ? $benefitTitle : 'Beneficio';

        if ($type === 'consult') {
            $name = trim($request->post('name', ''));
            $phone = trim($request->post('phone', ''));
            $medium = $request->post('contact_medium', '');
            $subject = trim($request->post('subject', ''));

            if ($name === '' || $phone === '' || $subject === '' || !in_array($medium, ['email', 'whatsapp'], true)) {
                return [
                    'success' => false,
                    'message' => 'Completa todos los campos de la consulta.',
                ];
            }

            $body = implode("\n", [
                '<p><strong>Tipo de solicitud:</strong> Consulta de beneficio</p>',
                '<p><strong>Beneficio:</strong> ' . Html::encode($benefitTitle) . '</p>',
                '<p><strong>Nombre:</strong> ' . Html::encode($name) . '</p>',
                '<p><strong>Teléfono:</strong> ' . Html::encode($phone) . '</p>',
                '<p><strong>Medio de contacto preferido:</strong> ' . Html::encode(ucfirst($medium)) . '</p>',
                '<p><strong>Consulta:</strong><br>' . nl2br(Html::encode($subject)) . '</p>',
            ]);

            $sent = Yii::$app->mailer->compose()
                ->setTo('info@camarainversionistas.com')
                ->setSubject('Consulta de beneficio - ' . $benefitTitle)
                ->setHtmlBody($body)
                ->send();

            return [
                'success' => (bool)$sent,
                'message' => $sent ? 'Tu consulta fue enviada.' : 'No se pudo enviar la consulta.',
            ];
        }

        if ($type === 'register') {
            $name = trim($request->post('name', ''));
            $phone = trim($request->post('phone', ''));
            $businessType = trim($request->post('business_type', ''));
            $businessName = trim($request->post('business_name', ''));
            $email = trim($request->post('email', ''));
            $address = trim($request->post('address', ''));
            $patentado = $request->post('patentado', '');
            $subject = trim($request->post('subject', ''));

            if ($name === '' || $phone === '' || $businessType === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $address === '' || $subject === '' || !in_array($patentado, ['si', 'no'], true)) {
                return [
                    'success' => false,
                    'message' => 'Completa todos los campos del registro.',
                ];
            }

            $body = implode("\n", [
                '<p><strong>Tipo de solicitud:</strong> Registro online</p>',
                '<p><strong>Beneficio:</strong> ' . Html::encode($benefitTitle) . '</p>',
                '<p><strong>Nombre:</strong> ' . Html::encode($name) . '</p>',
                '<p><strong>Teléfono:</strong> ' . Html::encode($phone) . '</p>',
                '<p><strong>Tipo de comercio:</strong> ' . Html::encode($businessType) . '</p>',
                $businessName !== '' ? '<p><strong>Nombre comercial:</strong> ' . Html::encode($businessName) . '</p>' : '',
                '<p><strong>Email:</strong> ' . Html::encode($email) . '</p>',
                '<p><strong>Dirección física:</strong> ' . Html::encode($address) . '</p>',
                '<p><strong>Patentado:</strong> ' . ($patentado === 'si' ? 'Sí' : 'No') . '</p>',
                '<p><strong>Asunto:</strong><br>' . nl2br(Html::encode(mb_substr($subject, 0, 500))) . '</p>',
            ]);

            $sent = Yii::$app->mailer->compose()
                ->setTo('info@camarainversionistas.com')
                ->setSubject('Registro online de beneficio - ' . $benefitTitle)
                ->setHtmlBody($body)
                ->send();

            return [
                'success' => (bool)$sent,
                'message' => $sent ? 'Tu registro fue enviado.' : 'No se pudo enviar el registro.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Tipo de solicitud inválido.',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $sort = Yii::$app->request->get('sort', 'id');
        $sort = $sort === 'name' ? 'name' : 'id';

        $featuredQuery = Business::find()
            ->where([
                'is_active' => true,
                'show_on_home' => true,
                'available_in_search' => true,
            ])
            ->with('categories');

        $featuredQuery->orderBy($sort === 'name' ? ['name' => SORT_ASC] : ['id' => SORT_ASC]);

        $featuredBusinesses = $featuredQuery->all();

        $businesses = Business::find()
            ->where([
                'is_active' => true,
                'available_in_search' => true,
            ])
            ->with('categories')
            ->orderBy(['name' => SORT_ASC])
            ->all();
        $sponsorSet = SponsorSet::find()->orderBy(['id' => SORT_ASC])->one();
        $siteConfig = SiteConfig::getCurrent();

        $alliesCount = Business::find()->where(['is_active' => true])->count();

        $locationStats = $this->buildLocationStats(100);

        $mapPoints = (new Query())
            ->select([
                'name' => 'b.name',
                'address' => 'b.address',
            ])
            ->from(['b' => Business::tableName()])
            ->where(['b.is_active' => true])
            ->andWhere(['not', ['b.address' => null]])
            ->andWhere(['<>', 'b.address', ''])
            ->all();

        $categoryStats = (new Query())
            ->select([
                'category_name' => 'c.name',
                'total' => 'COUNT(*)',
            ])
            ->from(['bc' => '{{%business_category}}'])
            ->innerJoin(['b' => Business::tableName()], 'b.id = bc.business_id')
            ->innerJoin(['c' => '{{%category}}'], 'c.id = bc.category_id')
            ->where(['b.is_active' => true])
            ->groupBy(['c.id', 'c.name'])
            ->orderBy(['total' => SORT_DESC])
            ->all();

        $categoryRanking = array_slice($categoryStats, 0, 25);

        $benefitCategories = BenefitCategory::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->with(['benefits' => static function ($query) {
                $query->andWhere(['is_active' => true])
                    ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
            }])
            ->all();

        return $this->render('index', [
            'businesses' => $businesses,
            'featuredBusinessIds' => array_map(static fn($business) => $business->id, $featuredBusinesses),
            'sort' => $sort,
            'sponsors' => $sponsorSet ? $sponsorSet->getImages() : [],
            'siteConfig' => $siteConfig,
            'alliesCount' => (int)$alliesCount,
            'locationStats' => $locationStats,
            'categoryRanking' => $categoryRanking,
            'categoryDistribution' => array_slice($categoryStats, 0, 100),
            'mapPoints' => $mapPoints,
            'benefitCategories' => $benefitCategories,
        ]);
    }

    public function actionLocationReport()
    {
        $locationStats = $this->buildLocationStats();

        if (empty($locationStats)) {
            Yii::$app->session->setFlash('error', 'No hay datos disponibles para generar el reporte.');
            return $this->redirect(['index']);
        }

        $pdf = new \TCPDF();
        $pdf->SetCreator('Cámara de Inversionistas');
        $pdf->SetAuthor('Cámara de Inversionistas');
        $pdf->SetTitle('Presencia por ubicación');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

        $date = Yii::$app->formatter->asDatetime(time(), 'php:d/m/Y H:i');
        $tableRows = '';
        foreach ($locationStats as $index => $row) {
            $rank = $index + 1;
            $location = Html::encode($row['location'] ?: 'SIN DIRECCIÓN');
            $total = (int)$row['total'];
            $tableRows .= sprintf(
                '<tr>
                    <td style="padding:6px;border:1px solid #ccc;text-align:center;">%d</td>
                    <td style="padding:6px;border:1px solid #ccc;">%s</td>
                    <td style="padding:6px;border:1px solid #ccc;text-align:center;">%d</td>
                </tr>',
                $rank,
                $location,
                $total
            );
        }

        $html = <<<HTML
            <h2 style="text-align:center;">Presencia por ubicación</h2>
            <p style="text-align:center;">Reporte generado el {$date}</p>
            <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin-top:15px;font-size:12px;">
                <thead>
                    <tr>
                        <th style="padding:6px;border:1px solid #ccc;width:60px;">#</th>
                        <th style="padding:6px;border:1px solid #ccc;">Ubicación</th>
                        <th style="padding:6px;border:1px solid #ccc;width:90px;">Aliados</th>
                    </tr>
                </thead>
                <tbody>
                    {$tableRows}
                </tbody>
            </table>
HTML;

        $pdf->writeHTML($html, true, false, true, false, '');
        $content = $pdf->Output('presencia-ubicaciones.pdf', 'S');

        return Yii::$app->response->sendContentAsFile(
            $content,
            'presencia-ubicaciones.pdf',
            [
                'mimeType' => 'application/pdf',
                'inline' => false,
            ]
        );
    }

    public function actionBenefitReport()
    {
        $categories = BenefitCategory::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->with([
                'benefits' => static function ($query) {
                    $query->andWhere(['is_active' => true])
                        ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
                },
            ])
            ->all();

        if (empty($categories)) {
            Yii::$app->session->setFlash('warning', 'No hay beneficios disponibles para descargar.');
            return $this->redirect(['index']);
        }

        $pdf = new \TCPDF();
        $pdf->SetCreator('Cámara de Inversionistas');
        $pdf->SetAuthor('Cámara de Inversionistas');
        $pdf->SetTitle('Listado de Beneficios');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

        $siteConfig = SiteConfig::getCurrent();
        $logoTag = '';
        $headerLogoPath = $this->resolveAbsolutePath('/images/benefits-report-logo.png')
            ?: $this->resolveAbsolutePath($siteConfig->logo_path);
        if ($headerLogoPath && !$this->isSvg($headerLogoPath)) {
            $pdf->Image($headerLogoPath, 160, 15, 35, 0, '', '', 'T', true, 300, '', false, false, 0, false, false, false);
        }
        $pdf->SetY(25);

        $contactInfo = '<p><strong>Info de contacto:</strong> 4070-0485 | info@camarainversionistas.com</p>';
        $html = <<<HTML
            <h1 style="text-align:center;margin-bottom:4px;">Listado de Beneficios</h1>
            <h2 style="text-align:center;margin-top:0;color:#0f172a;">Cámara de Inversionistas de Costa Rica</h2>
            {$contactInfo}
HTML;

        foreach ($categories as $category) {
            $categoryLogoPath = $this->resolveCatalogLogoPath($category->logo);
            $categoryLogoHtml = $this->renderLogoHtml($categoryLogoPath, LogoCatalog::getLabel($category->logo));
            $html .= '<div style="padding:10px 0;border-bottom:1px solid #ddd;">';
            $html .= '<h2 style="color:#0f172a;">' . Html::encode($category->name) . '</h2>';
            if ($categoryLogoHtml) {
                $html .= '<div>' . $categoryLogoHtml . '</div>';
            }
            if ($category->description) {
                $html .= '<p style="color:#4b5563;">' . Html::encode($category->description) . '</p>';
            }

            if (empty($category->benefits)) {
                $html .= '<p><em>No hay beneficios registrados.</em></p>';
                $html .= '</div>';
                continue;
            }

            $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="margin-top:10px;font-size:11px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color:#e0f2fe;">';
            $html .= '<th width="30%">Categoría</th>';
            $html .= '<th width="70%">Beneficio</th>';
            $html .= '</tr>';
            $html .= '</thead><tbody>';

            foreach ($category->benefits as $benefit) {
                $benefitLogoPath = $this->resolveCatalogLogoPath($benefit->logo) ?: $categoryLogoPath;
                $logoCell = $this->renderLogoHtml($benefitLogoPath, LogoCatalog::getLabel($benefit->logo));
                if (!$logoCell) {
                    $logoCell = Html::encode(LogoCatalog::getLabel($benefit->logo) ?: $category->name);
                }
                $html .= '<tr>';
                $html .= '<td align="center">' . $logoCell . '</td>';
                $html .= '<td><strong>' . Html::encode($benefit->title) . '</strong></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $content = $pdf->Output('listado-beneficios.pdf', 'S');

        return Yii::$app->response->sendContentAsFile(
            $content,
            'listado-beneficios.pdf',
            [
                'mimeType' => 'application/pdf',
                'inline' => false,
            ]
        );
    }


    private function buildLocationStats(int $limit = null): array
    {
        $query = (new Query())
            ->select([
                'location' => 'b.address',
                'total' => 'COUNT(*)',
            ])
            ->from(['b' => Business::tableName()])
            ->where(['b.is_active' => true])
            ->andWhere(['not', ['b.address' => null]])
            ->andWhere(['<>', 'b.address', ''])
            ->groupBy(['b.address'])
            ->orderBy(['total' => SORT_DESC]);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->all();
    }

    private function resolveCatalogLogoPath(?string $key): ?string
    {
        $relative = $key ? LogoCatalog::getRelativePath($key) : null;
        if (!$relative && $key) {
            $relative = $key;
        }
        return $this->resolveAbsolutePath($relative);
    }

    private function resolveAbsolutePath(?string $relative): ?string
    {
        if (!$relative) {
            return null;
        }
        if (preg_match('#^https?://#i', $relative)) {
            return null;
        }
        if (preg_match('/^[A-Za-z]:\\\\/', $relative)) {
            $absolute = $relative;
        } elseif (str_starts_with($relative, '/')) {
            $absolute = Yii::getAlias('@frontend/web') . $relative;
        } else {
            $absolute = Yii::getAlias('@frontend/web') . DIRECTORY_SEPARATOR . ltrim($relative, '/');
        }
        if (!is_file($absolute)) {
            return null;
        }
        return str_replace('\\', '/', $absolute);
    }

    private function isSvg(string $path): bool
    {
        return str_ends_with(strtolower($path), '.svg');
    }

    private function renderLogoHtml(?string $path, ?string $label): ?string
    {
        if ($path && !$this->isSvg($path)) {
            return '<img src="' . $path . '" height="40">';
        }
        if ($label) {
            return '<span style="font-weight:bold;">' . Html::encode($label) . '</span>';
        }
        return null;
    }

    public function actionContactBusiness()
    {
        $model = new ContactBusinessForm();

        if (!$model->load(Yii::$app->request->post())) {
            throw new BadRequestHttpException('Solicitud inválida.');
        }

        $isJson = Yii::$app->request->accepts('application/json') || Yii::$app->request->isAjax;

        if ($model->submit()) {
            if ($isJson) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Tu mensaje fue enviado al comercio.',
                ]);
            }

            Yii::$app->session->setFlash('success', 'Tu mensaje fue enviado al comercio.');
            return $this->redirect(['index']);
        }

        if ($isJson) {
            return $this->asJson([
                'success' => false,
                'errors' => $model->getFirstErrors(),
            ]);
        }

        Yii::$app->session->setFlash('error', reset($model->getFirstErrors()) ?: 'No se pudo enviar el mensaje.');
        return $this->redirect(['index']);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
