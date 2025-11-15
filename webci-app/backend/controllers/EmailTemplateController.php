<?php

namespace backend\controllers;

use backend\models\EmailTemplateSearch;
use common\models\EmailTemplate;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EmailTemplateController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('coming-soon');
    }

    public function actionCreate()
    {
        throw new NotFoundHttpException('Sección disponible próximamente.');
    }

    public function actionUpdate(int $id)
    {
        throw new NotFoundHttpException('Sección disponible próximamente.');
    }

    public function actionDelete(int $id)
    {
        throw new NotFoundHttpException('Sección disponible próximamente.');
    }

    protected function findModel(int $id): EmailTemplate
    {
        if (($model = EmailTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La plantilla solicitada no existe.');
    }
}

