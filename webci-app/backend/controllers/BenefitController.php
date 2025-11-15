<?php

namespace backend\controllers;

use backend\models\BenefitCategorySearch;
use backend\models\BenefitSearch;
use common\models\Benefit;
use common\models\BenefitCategory;
use common\services\LogoCatalog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BenefitController extends Controller
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
                    'delete-benefit' => ['POST'],
                    'delete-category' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(string $tab = 'benefits', ?int $benefitId = null, ?int $categoryId = null): string
    {
        $benefitSearchModel = new BenefitSearch();
        $categorySearchModel = new BenefitCategorySearch();

        $queryParams = Yii::$app->request->queryParams;
        $benefitDataProvider = $benefitSearchModel->search($queryParams);
        $categoryDataProvider = $categorySearchModel->search($queryParams);

        $benefitForm = $benefitId ? $this->findBenefit($benefitId) : new Benefit();
        if ($benefitForm->isNewRecord) {
            $benefitForm->loadDefaultValues();
        }

        $categoryForm = $categoryId ? $this->findCategory($categoryId) : new BenefitCategory();
        if ($categoryForm->isNewRecord) {
            $categoryForm->loadDefaultValues();
        }

        $request = Yii::$app->request;
        $postedTab = $request->post('activeTab');

        if ($benefitForm->load($request->post())) {
            if ($benefitForm->save()) {
                Yii::$app->session->setFlash('success', 'Beneficio guardado correctamente.');
                return $this->redirect(['index', 'tab' => 'benefits']);
            }
            $tab = 'benefits';
        } elseif ($categoryForm->load($request->post())) {
            if ($categoryForm->save()) {
                Yii::$app->session->setFlash('success', 'Categoría guardada correctamente.');
                return $this->redirect(['index', 'tab' => 'categories']);
            }
            $tab = 'categories';
        } elseif ($postedTab) {
            $tab = $postedTab;
        }

        return $this->render('index', [
            'benefitSearchModel' => $benefitSearchModel,
            'benefitDataProvider' => $benefitDataProvider,
            'categorySearchModel' => $categorySearchModel,
            'categoryDataProvider' => $categoryDataProvider,
            'benefitForm' => $benefitForm,
            'categoryForm' => $categoryForm,
            'categoryOptions' => BenefitCategory::getList(),
            'logoOptions' => LogoCatalog::options(),
            'activeTab' => in_array($tab, ['benefits', 'categories'], true) ? $tab : 'benefits',
        ]);
    }

    public function actionDeleteBenefit(int $id)
    {
        $this->findBenefit($id)->delete();
        Yii::$app->session->setFlash('info', 'Beneficio eliminado.');

        return $this->redirect(['index', 'tab' => 'benefits']);
    }

    public function actionDeleteCategory(int $id)
    {
        if (Benefit::find()->where(['category_id' => $id])->exists()) {
            Yii::$app->session->setFlash('error', 'No puedes eliminar la categoría porque tiene beneficios asociados.');
            return $this->redirect(['index', 'tab' => 'categories']);
        }

        $this->findCategory($id)->delete();
        Yii::$app->session->setFlash('info', 'Categoría eliminada.');

        return $this->redirect(['index', 'tab' => 'categories']);
    }

    protected function findBenefit(int $id): Benefit
    {
        if (($model = Benefit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El beneficio solicitado no existe.');
    }

    protected function findCategory(int $id): BenefitCategory
    {
        if (($model = BenefitCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La categoría solicitada no existe.');
    }
}

