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
        $searchModel = new EmailTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new EmailTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Plantilla creada.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Plantilla actualizada.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        if ($model->businesses) {
            Yii::$app->session->setFlash('error', 'No se puede eliminar una plantilla asignada a aliados.');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('info', 'Plantilla eliminada.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): EmailTemplate
    {
        if (($model = EmailTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La plantilla solicitada no existe.');
    }
}

