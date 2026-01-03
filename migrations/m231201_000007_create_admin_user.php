<?php

use yii\db\Migration;
use app\models\User;

/**
 * Creates default admin user.
 */
class m231201_000007_create_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = new User();
        $user->username = 'admin';
        $user->email = 'admin@example.com';
        $user->setPassword('admin123');
        $user->generateAuthKey();
        $user->role = 'admin';
        $user->status = User::STATUS_ACTIVE;
        $user->created_at = time();
        $user->updated_at = time();
        $user->save(false);

        // Asignar rol admin
        $authManager = Yii::$app->authManager;
        $adminRole = $authManager->getRole('admin');
        if ($adminRole) {
            $authManager->assign($adminRole, $user->id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $user = User::findByUsername('admin');
        if ($user) {
            $authManager = Yii::$app->authManager;
            $authManager->revokeAll($user->id);
            $user->delete();
        }
    }
}

