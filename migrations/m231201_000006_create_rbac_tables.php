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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Crear tabla auth_rule
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        // Crear tabla auth_item
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_item_rule_name',
            '{{%auth_item}}',
            'rule_name',
            '{{%auth_rule}}',
            'name',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('idx_auth_item_type', '{{%auth_item}}', 'type');

        // Crear tabla auth_item_child
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_item_child_parent',
            '{{%auth_item_child}}',
            'parent',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_auth_item_child_child',
            '{{%auth_item_child}}',
            'child',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Crear tabla auth_assignment
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_assignment_item_name',
            '{{%auth_assignment}}',
            'item_name',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Ahora crear los roles y permisos usando el authManager
        $authManager = Yii::$app->authManager;

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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
    }
}

