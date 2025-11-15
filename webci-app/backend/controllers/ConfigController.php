<?php

namespace backend\controllers;

use backend\models\SiteConfigForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
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

    public function actionIndex(): string|Response
    {
        $model = new SiteConfigForm();

        if (Yii::$app->request->isPost) {
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Logo actualizado correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'currentLogoPath' => SiteConfigForm::currentLogoPath(),
        ]);
    }
}

