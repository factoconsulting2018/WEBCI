<?php

namespace backend\controllers;

use common\models\SponsorSet;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class SponsorController extends Controller
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
        $model = SponsorSet::find()->orderBy(['id' => SORT_ASC])->one();
        if ($model === null) {
            $model = new SponsorSet();
            $model->title = 'Patrocinadores';
            $model->save(false);
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->imageUploadOne = UploadedFile::getInstance($model, 'imageUploadOne');
            $model->imageUploadTwo = UploadedFile::getInstance($model, 'imageUploadTwo');
            $model->imageUploadThree = UploadedFile::getInstance($model, 'imageUploadThree');
            $model->imageUploadFour = UploadedFile::getInstance($model, 'imageUploadFour');

            if ($model->save()) {
                $model->uploadImages();
                Yii::$app->session->setFlash('success', 'Patrocinadores actualizados.');
                return $this->refresh();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}

