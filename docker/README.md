# Docker Setup - Tienda Online

## Desarrollo Local

### Requisitos
- Docker
- Docker Compose

### Inicio Rápido

1. Copiar archivo de configuración de base de datos para Docker:
```bash
cp config/db-docker.php config/db.php
```

2. Construir e iniciar contenedores:
```bash
docker-compose up -d --build
```

3. Instalar dependencias:
```bash
docker-compose exec web composer install
```

4. Ejecutar migraciones:
```bash
docker-compose exec web php yii migrate
```

5. Acceder a la aplicación:
   - Frontend: http://localhost:8081
   - Admin: http://localhost:8081/admin/login
   - phpMyAdmin: http://localhost:8082

### Comandos Útiles

- Ver logs: `docker-compose logs -f`
- Detener contenedores: `docker-compose down`
- Reiniciar contenedores: `docker-compose restart`
- Acceder al contenedor: `docker-compose exec web bash`
- Ejecutar comandos Yii: `docker-compose exec web php yii [comando]`

### Puertos
- **8081**: Nginx (Web)
- **3307**: MySQL
- **8082**: phpMyAdmin

## Producción (EC2 Amazon Linux)

### Preparación en el Servidor

1. Instalar Docker y Docker Compose:
```bash
sudo yum update -y
sudo yum install docker -y
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -a -G docker ec2-user

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

2. Clonar/copiar el proyecto al servidor

3. Configurar variables de entorno:
```bash
cp .docker-env.example .docker-env
# Editar .docker-env con valores de producción
```

4. Configurar base de datos:
```bash
cp config/db-docker.php config/db.php
# Editar config/db.php si es necesario
```

### Despliegue

1. Construir imágenes:
```bash
docker-compose -f docker-compose.prod.yml build
```

2. Iniciar servicios:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

3. Instalar dependencias:
```bash
docker-compose -f docker-compose.prod.yml exec web composer install --no-dev --optimize-autoloader
```

4. Ejecutar migraciones:
```bash
docker-compose -f docker-compose.prod.yml exec web php yii migrate
```

5. Establecer permisos:
```bash
docker-compose -f docker-compose.prod.yml exec web chmod -R 777 runtime web/uploads
```

### Configuración SSL (Opcional)

1. Colocar certificados SSL en `docker/nginx/ssl/`:
   - `cert.pem` (certificado)
   - `key.pem` (clave privada)

2. Descomentar líneas SSL en `docker/nginx/nginx-prod.conf`

3. Reiniciar nginx:
```bash
docker-compose -f docker-compose.prod.yml restart nginx
```

### Mantenimiento

- Ver logs: `docker-compose -f docker-compose.prod.yml logs -f`
- Reiniciar servicios: `docker-compose -f docker-compose.prod.yml restart`
- Backup de base de datos:
```bash
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p tienda_online > backup.sql
```

### Puertos Producción
- **80**: HTTP
- **443**: HTTPS (si se configura SSL)

### Seguridad

- Cambiar todas las contraseñas por defecto
- Configurar firewall (Security Groups en AWS)
- Usar SSL/TLS en producción
- Configurar backups regulares de la base de datos
- Mantener Docker y las imágenes actualizadas

