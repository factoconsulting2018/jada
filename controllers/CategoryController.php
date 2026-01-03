<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Product;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the frontend actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * Displays products by category.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $category = $this->findModel($id);
        
        if ($category->status != Category::STATUS_ACTIVE) {
            throw new NotFoundHttpException('La categoría solicitada no está disponible.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Product::find()
                ->where(['category_id' => $category->id, 'status' => Product::STATUS_ACTIVE]),
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->all();

        return $this->render('view', [
            'category' => $category,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}

