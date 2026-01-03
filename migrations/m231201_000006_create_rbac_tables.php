<?php

use yii\db\Migration;

/**
 * Handles the creation of RBAC tables.
 */
class m231201_000006_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authManager = Yii::$app->authManager;
        $authManager->init();

        // Crear roles
        $admin = $authManager->createRole('admin');
        $admin->description = 'Administrador del sistema';
        $authManager->add($admin);

        $manager = $authManager->createRole('manager');
        $manager->description = 'Manager/Vendedor';
        $authManager->add($manager);

        $cliente = $authManager->createRole('cliente');
        $cliente->description = 'Cliente';
        $authManager->add($cliente);

        // Crear permisos
        $permissions = [
            'manageProducts' => 'Gestionar productos',
            'manageCategories' => 'Gestionar categorías',
            'manageBanners' => 'Gestionar banners',
            'manageClients' => 'Gestionar clientes',
            'manageUsers' => 'Gestionar usuarios',
            'accessAdmin' => 'Acceder al panel administrativo',
        ];

        foreach ($permissions as $name => $description) {
            $permission = $authManager->createPermission($name);
            $permission->description = $description;
            $authManager->add($permission);
        }

        // Asignar permisos a roles
        $authManager->addChild($admin, $authManager->getPermission('manageProducts'));
        $authManager->addChild($admin, $authManager->getPermission('manageCategories'));
        $authManager->addChild($admin, $authManager->getPermission('manageBanners'));
        $authManager->addChild($admin, $authManager->getPermission('manageClients'));
        $authManager->addChild($admin, $authManager->getPermission('manageUsers'));
        $authManager->addChild($admin, $authManager->getPermission('accessAdmin'));

        $authManager->addChild($manager, $authManager->getPermission('manageProducts'));
        $authManager->addChild($manager, $authManager->getPermission('manageCategories'));
        $authManager->addChild($manager, $authManager->getPermission('manageBanners'));
        $authManager->addChild($manager, $authManager->getPermission('manageClients'));
        $authManager->addChild($manager, $authManager->getPermission('accessAdmin'));

        // Asignar admin al primer usuario (se creará después)
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $authManager = Yii::$app->authManager;
        
        $authManager->removeAll();
    }
}

