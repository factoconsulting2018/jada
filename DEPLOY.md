# Guía de Despliegue en Producción - EC2 Ubuntu

Esta guía contiene todos los comandos necesarios para desplegar la aplicación en un servidor EC2 con Ubuntu.

## Prerequisitos

- Instancia EC2 t3.large con Ubuntu 22.04 LTS
- Acceso SSH al servidor
- Dominio multiserviciosdeoccidente.com apuntando a la IP pública de EC2

## Paso 1: Preparación del Servidor

### 1.1 Actualizar sistema

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalar Docker

```bash
# Instalar dependencias previas
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release

# Agregar clave GPG oficial de Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Agregar repositorio de Docker
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker Engine
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Agregar usuario actual al grupo docker
sudo usermod -aG docker $USER
newgrp docker

# Habilitar Docker al inicio
sudo systemctl enable docker
sudo systemctl start docker

# Verificar instalación
docker --version
docker compose version
```

### 1.3 Instalar Git

```bash
sudo apt install -y git
```

## Paso 2: Clonar el Proyecto

```bash
# Navegar al directorio donde se instalará la aplicación
cd /opt  # o /var/www según preferencia

# Clonar el repositorio
sudo git clone https://github.com/factoconsulting2018/jada.git tienda-online
sudo chown -R $USER:$USER tienda-online
cd tienda-online
```

## Paso 3: Configuración de Variables de Entorno

### 3.1 Crear archivo .env

```bash
# Crear archivo .env con las variables necesarias
cat > .env << 'EOF'
MYSQL_DATABASE=tienda_online
MYSQL_USER=tienda_user
MYSQL_PASSWORD=CambiarPasswordSeguro123!
MYSQL_ROOT_PASSWORD=CambiarRootPasswordSeguro123!
EOF

# IMPORTANTE: Editar y cambiar las contraseñas por valores seguros
nano .env
```

### 3.2 Configurar base de datos para Docker

```bash
cp config/db-docker.php config/db.php
```

## Paso 4: Configurar Firewall (UFW)

```bash
# Permitir SSH (CRÍTICO - no cerrar esta sesión hasta verificar)
sudo ufw allow 22/tcp

# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Habilitar firewall
sudo ufw enable

# Verificar estado
sudo ufw status
```

## Paso 5: Verificar Configuración DNS

**IMPORTANTE**: Asegurar que el dominio apunte a la IP pública de la instancia EC2:
- Registro A: `multiserviciosdeoccidente.com` → IP pública de EC2
- Registro A: `www.multiserviciosdeoccidente.com` → IP pública de EC2

Verificar con:
```bash
dig multiserviciosdeoccidente.com
nslookup multiserviciosdeoccidente.com
```

## Paso 6: Construir y Desplegar la Aplicación

**IMPORTANTE**: Antes de construir, asegúrate de que el archivo `.env` existe y contiene todas las variables necesarias:
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`
- `MYSQL_ROOT_PASSWORD`

```bash
# Construir las imágenes Docker
docker compose -f docker-compose.prod.yml build

# Iniciar los servicios
docker compose -f docker-compose.prod.yml up -d

# Esperar unos segundos para que MySQL esté listo
sleep 10

# Verificar que los contenedores estén corriendo
docker compose -f docker-compose.prod.yml ps

# Si algún contenedor está en estado "Restarting", verificar logs
docker compose -f docker-compose.prod.yml logs web
docker compose -f docker-compose.prod.yml logs db
```

## Paso 7: Instalar Dependencias y Ejecutar Migraciones

```bash
# Instalar dependencias de Composer (producción)
docker compose -f docker-compose.prod.yml exec web composer install --no-dev --optimize-autoloader

# Configurar permisos
docker compose -f docker-compose.prod.yml exec web chmod -R 777 runtime web/uploads

# Ejecutar migraciones de base de datos
docker compose -f docker-compose.prod.yml exec web php yii migrate --interactive=0
```

## Paso 8: Configurar SSL/HTTPS con Let's Encrypt

### 8.1 Instalar Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 8.2 Obtener certificados SSL

**NOTA**: El dominio debe estar apuntando a la IP de EC2 antes de ejecutar este paso.

```bash
# Detener nginx temporalmente para usar certbot en modo standalone
docker compose -f docker-compose.prod.yml stop nginx

# Obtener certificados
sudo certbot certonly --standalone -d multiserviciosdeoccidente.com -d www.multiserviciosdeoccidente.com

# Reiniciar nginx
docker compose -f docker-compose.prod.yml start nginx
```

### 8.3 Configurar certificados en Docker

```bash
# Crear directorio para certificados
sudo mkdir -p docker/nginx/ssl

# Copiar certificados
sudo cp /etc/letsencrypt/live/multiserviciosdeoccidente.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/multiserviciosdeoccidente.com/privkey.pem docker/nginx/ssl/key.pem

# Configurar permisos
sudo chmod 644 docker/nginx/ssl/cert.pem
sudo chmod 600 docker/nginx/ssl/key.pem
sudo chown -R $USER:$USER docker/nginx/ssl
```

### 8.4 Habilitar SSL en Nginx

Editar `docker/nginx/nginx-prod.conf`:

1. Descomentar las líneas SSL:
   - `ssl_certificate /etc/nginx/ssl/cert.pem;`
   - `ssl_certificate_key /etc/nginx/ssl/key.pem;`
   - `ssl_protocols TLSv1.2 TLSv1.3;`
   - `ssl_ciphers HIGH:!aNULL:!MD5;`

2. Descomentar el redirect a HTTPS en el bloque HTTP (línea 4):
   - `return 301 https://$host$request_uri;`

3. Reiniciar nginx:
```bash
docker compose -f docker-compose.prod.yml restart nginx
```

## Paso 9: Configurar Renovación Automática de SSL

```bash
# Agregar tarea cron para renovar certificados
sudo crontab -e

# Agregar esta línea (ajustar la ruta según donde esté el proyecto)
0 0 1 * * certbot renew --quiet && cd /opt/tienda-online && docker compose -f docker-compose.prod.yml restart nginx
```

## Paso 10: Verificación Final

```bash
# Ver logs de los servicios
docker compose -f docker-compose.prod.yml logs -f

# Verificar que los servicios estén corriendo
docker compose -f docker-compose.prod.yml ps

# Probar acceso HTTP/HTTPS
curl -I http://multiserviciosdeoccidente.com
curl -I https://multiserviciosdeoccidente.com
```

## Paso 11: Seguridad Adicional

1. **Cambiar credenciales del admin**: 
   - Acceder a: `https://multiserviciosdeoccidente.com/admin/login`
   - Usuario: `admin`
   - Contraseña: `admin123` (CAMBIAR INMEDIATAMENTE)

2. **Configurar Security Groups en AWS EC2**:
   - Permitir puerto 22 (SSH) desde tu IP
   - Permitir puerto 80 (HTTP) desde 0.0.0.0/0
   - Permitir puerto 443 (HTTPS) desde 0.0.0.0/0

3. **Configurar backups automáticos** (recomendado):
```bash
# Crear script de backup
cat > /opt/backup-db.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/opt/backups"
mkdir -p $BACKUP_DIR
cd /opt/tienda-online
docker compose -f docker-compose.prod.yml exec -T db mysqldump -u root -p$MYSQL_ROOT_PASSWORD tienda_online > $BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql
# Eliminar backups más antiguos de 30 días
find $BACKUP_DIR -name "backup_*.sql" -mtime +30 -delete
EOF

chmod +x /opt/backup-db.sh

# Agregar a crontab (backup diario a las 2 AM)
sudo crontab -e
# Agregar: 0 2 * * * /opt/backup-db.sh
```

## Actualizar Cambios en el Servidor (Despliegue de Nuevos Cambios)

Si ya tienes la aplicación desplegada y necesitas actualizar con los últimos cambios del repositorio:

```bash
# 1. Conectarse al servidor vía SSH
# (Ya deberías estar conectado)

# 2. Navegar al directorio del proyecto
cd /opt/tienda-online  # o donde esté el proyecto

# 3. Hacer backup de la base de datos (RECOMENDADO)
docker compose -f docker-compose.prod.yml exec -T db mysqldump -u root -p${MYSQL_ROOT_PASSWORD} tienda_online > backup_$(date +%Y%m%d_%H%M%S).sql

# 4. Obtener los últimos cambios del repositorio
git pull origin main  # o la rama que uses (master, main, etc.)

# 5. Detener los servicios
docker compose -f docker-compose.prod.yml down

# 6. Reconstruir las imágenes (solo si hay cambios en Dockerfile o docker-compose)
docker compose -f docker-compose.prod.yml build

# 7. Iniciar los servicios
docker compose -f docker-compose.prod.yml up -d

# 8. Esperar a que MySQL esté listo
sleep 10

# 9. Verificar que los contenedores estén corriendo correctamente
docker compose -f docker-compose.prod.yml ps

# 10. Si hay nuevas migraciones, ejecutarlas
docker compose -f docker-compose.prod.yml exec web php yii migrate --interactive=0

# 11. Si hay nuevos cambios en dependencias, actualizar Composer
docker compose -f docker-compose.prod.yml exec web composer install --no-dev --optimize-autoloader

# 12. Verificar logs para asegurar que todo funciona
docker compose -f docker-compose.prod.yml logs --tail=50 web
docker compose -f docker-compose.prod.yml logs --tail=50 nginx
```

**Nota**: Si solo hay cambios en código PHP/JavaScript (sin cambios en Dockerfiles o docker-compose), puedes omitir los pasos 5 y 6, y simplemente hacer `git pull` seguido de un reinicio de servicios:
```bash
git pull
docker compose -f docker-compose.prod.yml restart web nginx
```

## Comandos de Mantenimiento

```bash
# Ver logs
docker compose -f docker-compose.prod.yml logs -f [servicio]

# Reiniciar servicios
docker compose -f docker-compose.prod.yml restart

# Reiniciar un servicio específico
docker compose -f docker-compose.prod.yml restart web
docker compose -f docker-compose.prod.yml restart nginx
docker compose -f docker-compose.prod.yml restart db

# Detener servicios
docker compose -f docker-compose.prod.yml down

# Detener y eliminar volúmenes (¡CUIDADO! Esto elimina la base de datos)
docker compose -f docker-compose.prod.yml down -v

# Backup manual de base de datos
docker compose -f docker-compose.prod.yml exec db mysqldump -u root -p${MYSQL_ROOT_PASSWORD} tienda_online > backup_$(date +%Y%m%d).sql

# Ver uso de recursos de los contenedores
docker stats

# Limpiar imágenes y contenedores no usados
docker system prune -a
```

## Solución de Problemas

### Verificar que los contenedores estén corriendo:
```bash
docker compose -f docker-compose.prod.yml ps
```

### Ver logs de errores:
```bash
docker compose -f docker-compose.prod.yml logs web
docker compose -f docker-compose.prod.yml logs nginx
docker compose -f docker-compose.prod.yml logs db
```

### Reiniciar un servicio específico:
```bash
docker compose -f docker-compose.prod.yml restart [servicio]
```

### Verificar conexión a la base de datos:
```bash
# Usando variable de entorno (recomendado)
docker compose -f docker-compose.prod.yml exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "SHOW DATABASES;"

# O de forma interactiva
docker compose -f docker-compose.prod.yml exec db mysql -u root -p -e "SHOW DATABASES;"
```

### El contenedor web está en estado "Restarting":
```bash
# Ver logs del contenedor web
docker compose -f docker-compose.prod.yml logs web

# Verificar que el archivo .env existe y tiene MYSQL_ROOT_PASSWORD
cat .env | grep MYSQL_ROOT_PASSWORD

# Verificar que el servicio db está corriendo
docker compose -f docker-compose.prod.yml ps db

# Ver logs de la base de datos
docker compose -f docker-compose.prod.yml logs db

# Si el problema persiste, verificar que mysql-client esté instalado en el contenedor
docker compose -f docker-compose.prod.yml exec web mysqladmin --version
```

### Problemas con variables de entorno:
```bash
# Verificar que el archivo .env existe
ls -la .env

# Verificar variables de entorno del contenedor web
docker compose -f docker-compose.prod.yml exec web env | grep MYSQL

# Si falta el archivo .env, crearlo (ver Paso 3.1)
```

