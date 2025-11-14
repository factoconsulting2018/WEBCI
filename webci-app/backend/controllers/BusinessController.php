<?php

namespace backend\controllers;

use backend\models\BusinessSearch;
use common\models\Business;
use common\models\Category;
use common\models\EmailTemplate;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class BusinessController extends Controller
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
        $searchModel = new BusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Business();
        $model->loadDefaultValues();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Aliado creado correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categoryItems' => $this->getCategoryItems(),
            'templateItems' => $this->getTemplateItems(),
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Aliado actualizado correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categoryItems' => $this->getCategoryItems(),
            'templateItems' => $this->getTemplateItems(),
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('info', 'Aliado eliminado.');

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Business
    {
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El aliado solicitado no existe.');
    }

    private function getCategoryItems(): array
    {
        return ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    private function getTemplateItems(): array
    {
        return ArrayHelper::map(EmailTemplate::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }
}

