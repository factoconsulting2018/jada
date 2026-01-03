# Tienda Online - E-commerce en Yii 2 con Material Design 3

Sistema de e-commerce completo desarrollado en Yii 2 con MySQL y Material Design 3.

## Características

- **Panel Administrativo** completo con gestión de productos, categorías, banners y clientes
- **Sistema de Roles (RBAC)**: Admin, Manager y Cliente
- **Frontend público** con catálogo estilo Shopify
- **Gestión de imágenes** múltiples por producto
- **Banner Hero** configurable desde el panel administrativo
- **Integración WhatsApp** para contacto directo con productos
- **100% Responsive** - Diseño adaptativo para móviles, tablets y desktop
- **Material Design 3** - Interfaz moderna y elegante

## Requisitos

- PHP >= 8.0
- MySQL 5.7+ o MariaDB 10.2+
- Composer
- Extensiones PHP: pdo_mysql, gd (para imágenes)

## Instalación

### Opción 1: Docker (Recomendado)

#### Desarrollo Local

1. Clonar o descargar el proyecto
2. Copiar configuración de base de datos:
```bash
cp config/db-docker.php config/db.php
```

3. Inicializar proyecto (construye, inicia contenedores, instala dependencias y ejecuta migraciones):
```bash
make init
```

O manualmente:
```bash
docker-compose up -d --build
docker-compose exec web composer install
docker-compose exec web php yii migrate
```

4. Acceder a:
   - Frontend: http://localhost:8081
   - Admin: http://localhost:8081/admin/login
   - phpMyAdmin: http://localhost:8082

Ver `docker/README.md` para más detalles.

#### Producción (EC2 Amazon Linux)

Ver instrucciones completas en `docker/README.md`.

### Opción 2: Instalación Tradicional

1. Clonar o descargar el proyecto
2. Instalar dependencias:
```bash
composer install
```

3. Configurar la base de datos en `config/db.php`:
```php
'dsn' => 'mysql:host=localhost;dbname=tienda_online',
'username' => 'tu_usuario',
'password' => 'tu_contraseña',
```

4. Crear la base de datos:
```bash
mysql -u root -p -e "CREATE DATABASE tienda_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

5. Ejecutar migraciones:
```bash
php yii migrate
```

6. Configurar el servidor web para apuntar a la carpeta `web/`

7. Configurar el número de WhatsApp en `config/params.php`:
```php
'whatsapp' => [
    'number' => '1234567890', // Sin + ni espacios
    'defaultMessage' => 'Hola, me interesa el siguiente producto:',
],
```

## Credenciales por defecto

- **Usuario**: admin
- **Contraseña**: admin123

**IMPORTANTE**: Cambiar la contraseña después de la primera instalación.

## Estructura del Proyecto

```
.
├── config/          # Archivos de configuración
├── controllers/     # Controladores del frontend
├── migrations/      # Migraciones de base de datos
├── models/          # Modelos de datos
├── modules/
│   └── admin/       # Módulo administrativo
│       ├── controllers/
│       └── views/
├── views/           # Vistas del frontend
├── web/             # Archivos públicos (assets, uploads)
│   ├── css/
│   ├── js/
│   └── uploads/
└── assets/          # Assets bundles
```

## Uso

### Panel Administrativo

Acceder a: `http://tu-dominio/admin/login`

- **Dashboard**: Estadísticas generales del sistema
- **Productos**: Gestión completa de productos (CRUD)
- **Categorías**: Gestión de categorías
- **Banners**: Gestión del banner hero de la página principal
- **Clientes**: Gestión de clientes

### Frontend

- **Inicio**: `http://tu-dominio/`
- **Catálogo**: `http://tu-dominio/products`
- **Producto**: `http://tu-dominio/product/view?id=X`
- **Categoría**: `http://tu-dominio/category/view?id=X`

## Desarrollo

### Ejecutar en desarrollo

```bash
php yii serve
```

Acceder a: `http://localhost:8080`

### Gii (Generador de Código)

Acceder a: `http://tu-dominio/gii`

Solo disponible en modo desarrollo (YII_ENV_DEV).

## Notas

- Las imágenes se almacenan en `web/uploads/`
- El sistema genera automáticamente las carpetas necesarias para subir imágenes
- Todos los formularios incluyen validación CSRF
- Las contraseñas se almacenan usando hash seguro

## Licencia

BSD-3-Clause

