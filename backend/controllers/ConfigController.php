<?php

namespace backend\controllers;

use common\models\SiteConfig;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class ConfigController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = SiteConfig::getCurrent();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');

            if ($model->validate()) {
                if ($model->logoFile) {
                    $model->uploadLogo();
                }
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Configuración actualizada correctamente.');
                    return $this->refresh();
                }
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}

