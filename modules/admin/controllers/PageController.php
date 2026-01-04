<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Page;
use app\models\MainMenuItem;
use app\models\FooterMenuItem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchQuery = Yii::$app->request->get('search', '');
        
        $query = Page::find();
        
        if (!empty($searchQuery)) {
            $query->andWhere(['or',
                ['like', 'title', $searchQuery],
                ['like', 'slug', $searchQuery],
            ]);
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['menu_order' => SORT_ASC, 'title' => SORT_ASC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Page model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Page();
        $model->status = Page::STATUS_ACTIVE;
        $model->show_in_menu = 0;
        $model->menu_order = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->syncMenuItems($model);
            Yii::$app->session->setFlash('success', 'Página creada exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->syncMenuItems($model);
            Yii::$app->session->setFlash('success', 'Página actualizada exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Página eliminada exitosamente.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model is not found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }

    /**
     * Sync menu items based on page's menu selection
     */
    private function syncMenuItems($page)
    {
        $addToMenu = Yii::$app->request->post('Page')['add_to_menu'] ?? 'none';
        $footerPosition = Yii::$app->request->post('Page')['footer_position'] ?? 1;
        $menuOrder = $page->menu_order ?? 0;

        // Handle Main Menu
        $mainMenuItem = MainMenuItem::find()->where(['page_id' => $page->id])->one();
        
        if (in_array($addToMenu, ['main', 'both'])) {
            // Create or update main menu item
            if (!$mainMenuItem) {
                $mainMenuItem = new MainMenuItem();
                $mainMenuItem->type = MainMenuItem::TYPE_PAGE;
                $mainMenuItem->page_id = $page->id;
                $mainMenuItem->label = $page->title;
                $mainMenuItem->order = $menuOrder;
                $mainMenuItem->status = $page->status == Page::STATUS_ACTIVE ? MainMenuItem::STATUS_ACTIVE : MainMenuItem::STATUS_INACTIVE;
            } else {
                $mainMenuItem->label = $page->title;
                $mainMenuItem->order = $menuOrder;
                $mainMenuItem->status = $page->status == Page::STATUS_ACTIVE ? MainMenuItem::STATUS_ACTIVE : MainMenuItem::STATUS_INACTIVE;
            }
            $mainMenuItem->save(false);
        } else {
            // Remove from main menu if exists
            if ($mainMenuItem) {
                $mainMenuItem->delete();
            }
        }

        // Handle Footer Menu
        $footerMenuItem = FooterMenuItem::find()->where(['page_id' => $page->id])->one();
        
        if (in_array($addToMenu, ['footer', 'both'])) {
            // Create or update footer menu item
            if (!$footerMenuItem) {
                $footerMenuItem = new FooterMenuItem();
                $footerMenuItem->page_id = $page->id;
                $footerMenuItem->position = (int)$footerPosition;
                $footerMenuItem->order = $menuOrder;
                $footerMenuItem->status = $page->status == Page::STATUS_ACTIVE ? FooterMenuItem::STATUS_ACTIVE : FooterMenuItem::STATUS_INACTIVE;
            } else {
                $footerMenuItem->position = (int)$footerPosition;
                $footerMenuItem->order = $menuOrder;
                $footerMenuItem->status = $page->status == Page::STATUS_ACTIVE ? FooterMenuItem::STATUS_ACTIVE : FooterMenuItem::STATUS_INACTIVE;
            }
            $footerMenuItem->save(false);
        } else {
            // Remove from footer menu if exists
            if ($footerMenuItem) {
                $footerMenuItem->delete();
            }
        }

        // Update show_in_menu for backward compatibility
        $page->show_in_menu = in_array($addToMenu, ['main', 'both']) ? 1 : 0;
        $page->save(false);
    }
}

