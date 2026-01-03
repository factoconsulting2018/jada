<?php

namespace app\modules\admin;

use Yii;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $defaultRoute = 'dashboard';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $this->layout = 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Excluir login del check de acceso
        if ($action->controller->id !== 'login') {
            $this->checkAccess();
        }
        return parent::beforeAction($action);
    }

    /**
     * Check if user has access to admin module
     */
    protected function checkAccess()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        } elseif (!Yii::$app->user->can('accessAdmin')) {
            throw new \yii\web\ForbiddenHttpException('No tienes permiso para acceder a esta sección.');
        }
    }
}

