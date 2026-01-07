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
sudo git clone git tienda-online
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

## Paso 4: Configurar Firewall

### 4.1 Configurar Security Group en AWS EC2 (CRÍTICO - HACER PRIMERO)

**IMPORTANTE**: El Security Group de AWS debe permitir tráfico entrante en los puertos 80 y 443 desde internet (0.0.0.0/0). **Este es el problema más común que causa el error "Timeout during connect" en Certbot.**

**Pasos en AWS Console:**

1. Ve a **EC2 Dashboard** → **Instances** → Selecciona tu instancia
2. En la pestaña **Security**, haz clic en el **Security Group** (ej: `sg-xxxxx`)
3. Haz clic en **Edit inbound rules**
4. Agrega estas reglas si no existen:
   - **Type**: HTTP, **Port**: 80, **Source**: 0.0.0.0/0, **Description**: "Allow HTTP for Let's Encrypt"
   - **Type**: HTTPS, **Port**: 443, **Source**: 0.0.0.0/0, **Description**: "Allow HTTPS"
   - **Type**: SSH, **Port**: 22, **Source**: Tu IP (o 0.0.0.0/0 solo si es necesario), **Description**: "SSH access"
5. Haz clic en **Save rules**

**Verificar desde el servidor:**
```bash
# Verificar que el puerto 80 está escuchando
sudo netstat -tulpn | grep :80

# Obtener IP pública de la instancia
curl -s ifconfig.me

# Probar acceso desde internet (reemplaza con tu IP pública)
curl -I http://$(curl -s ifconfig.me)
```

**Si el último comando falla o no responde**, el Security Group no está configurado correctamente.

### 4.2 Configurar Firewall Local (UFW) - Opcional

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

**NOTA**: Si UFW está bloqueando el puerto 80, deshabilítalo temporalmente para Certbot:
```bash
sudo ufw disable  # Solo si es necesario para Certbot
# Después de obtener certificados, reactivarlo:
sudo ufw enable
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

**IMPORTANTE**: Antes de construir, asegúrate de que:

1. **El archivo `.env` existe** y contiene todas las variables necesarias:
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_ROOT_PASSWORD`

2. **El archivo `docker-entrypoint-prod.sh` existe** en el directorio del proyecto:
```bash
# Verificar que el archivo existe
ls -la docker-entrypoint-prod.sh

# Si no existe, verificar que esté en el repositorio
git ls-files | grep docker-entrypoint-prod.sh
```

3. **Verificar que todos los archivos necesarios están presentes**:
```bash
# Verificar archivos críticos
ls -la Dockerfile.prod docker-compose.prod.yml docker-entrypoint-prod.sh .env
```

```bash
# Construir las imágenes Docker
docker compose -f docker-compose.prod.yml build

# Si hay errores durante el build, verificar:
# - Que docker-entrypoint-prod.sh existe
# - Que el archivo .env tiene todas las variables
# - Que no hay errores de sintaxis en Dockerfile.prod

# Iniciar los servicios
docker compose -f docker-compose.prod.yml up -d

# Esperar unos segundos para que MySQL esté listo
sleep 10

# Verificar que los contenedores estén corriendo
docker compose -f docker-compose.prod.yml ps

# Si algún contenedor está en estado "Restarting", verificar logs
docker compose -f docker-compose.prod.yml logs web
docker compose -f docker-compose.prod.yml logs db

# Si el contenedor web está reiniciando, verificar específicamente:
docker compose -f docker-compose.prod.yml logs web | tail -20
```

## Paso 7: Instalar Dependencias y Ejecutar Migraciones

```bash
# Instalar dependencias de Composer (producción)
# NOTA: Si aparece error de Git "dubious ownership", ejecutar primero:
docker compose -f docker-compose.prod.yml exec web git config --global --add safe.directory /var/www/html

# Luego ejecutar composer install
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

**NOTA IMPORTANTE**: Antes de ejecutar este paso, asegúrate de:

1. **El dominio apunta a la IP pública de EC2** (verificar con `dig multiserviciosdeoccidente.com`)
2. **El Security Group de AWS permite tráfico entrante en puerto 80** desde 0.0.0.0/0 (ver Paso 4.1)
3. **El puerto 80 es accesible desde internet** (verificar con `curl -I http://TU_IP_PUBLICA` desde otra máquina)
4. **Nginx está corriendo y sirve el sitio** (verificar con `docker compose -f docker-compose.prod.yml ps nginx`)

**Método 1: Usando modo standalone (requiere detener Nginx)**

```bash
# 1. Verificar qué está usando el puerto 80
sudo lsof -i :80
# o
sudo netstat -tulpn | grep :80

# 2. Detener todos los servicios que usen el puerto 80
docker compose -f docker-compose.prod.yml stop nginx

# Si hay nginx del sistema corriendo:
sudo systemctl stop nginx 2>/dev/null || true

# Si hay apache corriendo:
sudo systemctl stop apache2 2>/dev/null || true

# 3. Verificar que el puerto 80 esté libre
sudo lsof -i :80
# No debe mostrar nada (o solo mostrar certbot después de iniciarlo)

# 4. Obtener certificados
sudo certbot certonly --standalone -d multiserviciosdeoccidente.com -d www.multiserviciosdeoccidente.com

# 5. Reiniciar nginx
docker compose -f docker-compose.prod.yml start nginx
```

**Método 2: Usando plugin webroot (NO requiere detener Nginx) - RECOMENDADO**

```bash
# 1. Crear directorio para el desafío de Certbot
sudo mkdir -p /var/www/certbot

# 2. Obtener certificados usando webroot (Nginx debe estar corriendo)
sudo certbot certonly --webroot -w /var/www/certbot -d multiserviciosdeoccidente.com -d www.multiserviciosdeoccidente.com

# NOTA: Este método requiere que Nginx esté configurado para servir el directorio /var/www/certbot
# Si no funciona, usar el Método 1
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

# 3. Verificar estado actual de los contenedores
docker compose -f docker-compose.prod.yml ps

# 4. Hacer backup de la base de datos (RECOMENDADO)
# Solo si el contenedor db está corriendo
if docker compose -f docker-compose.prod.yml ps db | grep -q "Up"; then
    docker compose -f docker-compose.prod.yml exec -T db mysqldump -u root -p${MYSQL_ROOT_PASSWORD} tienda_online > backup_$(date +%Y%m%d_%H%M%S).sql
    echo "Backup creado exitosamente"
else
    echo "ADVERTENCIA: El contenedor db no está corriendo. No se pudo hacer backup."
fi

# 5. Verificar qué rama usar en git
git branch -r
# Si la rama es 'master' en lugar de 'main', usar: git pull origin master

# 6. Obtener los últimos cambios del repositorio
git pull origin main  # o 'master' según tu rama principal

# 7. Verificar que docker-entrypoint-prod.sh existe después del pull
if [ ! -f "docker-entrypoint-prod.sh" ]; then
    echo "ERROR: docker-entrypoint-prod.sh no existe. Verificar que esté en el repositorio."
    exit 1
fi

# 8. Verificar que el archivo .env existe
if [ ! -f ".env" ]; then
    echo "ERROR: El archivo .env no existe. Crearlo antes de continuar (ver Paso 3.1)."
    exit 1
fi

# 9. Detener los servicios
docker compose -f docker-compose.prod.yml down

# 10. Reconstruir las imágenes (necesario si hay cambios en Dockerfile o docker-compose)
docker compose -f docker-compose.prod.yml build

# 11. Iniciar los servicios
docker compose -f docker-compose.prod.yml up -d

# 12. Esperar a que MySQL esté listo
sleep 10

# 13. Verificar que los contenedores estén corriendo correctamente
docker compose -f docker-compose.prod.yml ps

# 14. Si algún contenedor está en "Restarting", diagnosticar:
if docker compose -f docker-compose.prod.yml ps | grep -q "Restarting"; then
    echo "ADVERTENCIA: Algunos contenedores están reiniciando. Verificando logs..."
    docker compose -f docker-compose.prod.yml logs web | tail -30
    docker compose -f docker-compose.prod.yml logs db | tail -30
    echo "Revisa los logs arriba para identificar el problema."
    exit 1
fi

# 15. Si hay nuevas migraciones, ejecutarlas
docker compose -f docker-compose.prod.yml exec web php yii migrate --interactive=0

# 16. Si hay nuevos cambios en dependencias, actualizar Composer
docker compose -f docker-compose.prod.yml exec web composer install --no-dev --optimize-autoloader

# 17. Verificar logs para asegurar que todo funciona
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
# Ver logs del contenedor web (últimas 50 líneas)
docker compose -f docker-compose.prod.yml logs web | tail -50

# Error común: "exec /usr/local/bin/docker-entrypoint.sh: no such file or directory"
# CAUSA: El script usa #!/bin/bash pero Alpine Linux no tiene bash instalado
# SOLUCIÓN: Cambiar el shebang a #!/bin/sh en docker-entrypoint-prod.sh

# 1. Verificar que docker-entrypoint-prod.sh existe en el servidor
ls -la docker-entrypoint-prod.sh

# 2. Verificar el shebang del archivo (debe ser #!/bin/sh, NO #!/bin/bash)
head -1 docker-entrypoint-prod.sh

# 3. Si muestra #!/bin/bash, actualizar el repositorio y hacer pull
git pull origin main  # o master

# 4. Verificar que el cambio se aplicó
head -1 docker-entrypoint-prod.sh

# 5. Reconstruir la imagen SIN cache para asegurar que se aplica el cambio
docker compose -f docker-compose.prod.yml build --no-cache web

# 6. Reiniciar el contenedor
docker compose -f docker-compose.prod.yml up -d web

# 7. Verificar que el contenedor está corriendo
docker compose -f docker-compose.prod.yml ps web

# Verificar que el archivo .env existe y tiene MYSQL_ROOT_PASSWORD
cat .env | grep MYSQL_ROOT_PASSWORD

# Verificar que el servicio db está corriendo
docker compose -f docker-compose.prod.yml ps db

# Ver logs de la base de datos
docker compose -f docker-compose.prod.yml logs db | tail -30

# Si el problema persiste, verificar que mysql-client esté instalado en el contenedor
# (Solo si puedes acceder al contenedor)
docker compose -f docker-compose.prod.yml exec web mysqladmin --version 2>&1 || echo "No se puede acceder al contenedor"
```

### Problemas con variables de entorno:
```bash
# Verificar que el archivo .env existe
ls -la .env

# Verificar variables de entorno del contenedor web
docker compose -f docker-compose.prod.yml exec web env | grep MYSQL

# Si falta el archivo .env, crearlo (ver Paso 3.1)
```

### Error "No space left on device" durante el build:

Este error ocurre cuando el servidor no tiene suficiente espacio en disco. Sigue estos pasos:

#### 1. Verificar espacio en disco:
```bash
# Ver uso de espacio en disco
df -h

# Ver uso detallado de directorios
du -sh /* 2>/dev/null | sort -hr | head -10

# Ver uso de espacio de Docker
docker system df
```

#### 2. Limpiar espacio de Docker (RECOMENDADO primero):

```bash
# Ver qué ocupa espacio en Docker
docker system df -v

# Limpiar contenedores detenidos, redes no usadas, imágenes huérfanas y caché de build
docker system prune -a --volumes

# Si necesitas más espacio, eliminar todas las imágenes no usadas (CUIDADO: esto elimina todas las imágenes que no están en uso)
docker image prune -a

# Eliminar volúmenes no usados (CUIDADO: esto elimina volúmenes no asociados a contenedores)
docker volume prune

# Limpiar caché de build de Docker (puede liberar mucho espacio)
docker builder prune -a
```

#### 3. Limpiar espacio del sistema:

```bash
# Limpiar caché de paquetes APT
sudo apt clean
sudo apt autoremove -y

# Limpiar logs del sistema (logs antiguos)
sudo journalctl --vacuum-time=7d  # Elimina logs de más de 7 días
sudo journalctl --vacuum-size=100M  # O mantener solo 100MB de logs

# Buscar archivos grandes para eliminar manualmente
find / -type f -size +100M 2>/dev/null | head -20
```

#### 4. Limpiar espacio específico del proyecto:

```bash
# Verificar backups antiguos
ls -lh /opt/tienda-online/backup_*.sql 2>/dev/null

# Eliminar backups antiguos (más de 7 días)
find /opt/tienda-online -name "backup_*.sql" -mtime +7 -delete

# Limpiar logs de Docker del proyecto
docker compose -f docker-compose.prod.yml logs --no-log-prefix 2>&1 | head -100 > /dev/null
```

#### 5. Después de limpiar, reconstruir:

```bash
# Verificar espacio disponible después de limpiar
df -h /

# Si hay suficiente espacio (al menos 2-3GB libres), reconstruir
cd /opt/tienda-online
docker compose -f docker-compose.prod.yml build web

# Si el build aún falla, reconstruir sin caché pero después de limpiar más
docker compose -f docker-compose.prod.yml build --no-cache web
```

#### 6. Si el problema persiste - Aumentar espacio en AWS EC2:

Si después de limpiar aún no hay suficiente espacio, necesitarás aumentar el volumen EBS de la instancia EC2:

1. **En AWS Console:**
   - EC2 → Volumes → Seleccionar el volumen de la instancia
   - Actions → Modify Volume
   - Aumentar el tamaño (recomendado: mínimo 20GB para producción)
   - Esperar a que complete la modificación

2. **En el servidor (después de aumentar el volumen):**
```bash
# Verificar el nuevo tamaño
lsblk

# Extender la partición (si es necesario, según tu sistema)
sudo growpart /dev/xvda1 1  # Ajustar según tu dispositivo
sudo resize2fs /dev/xvda1   # Para ext4

# Verificar el nuevo espacio
df -h
```

#### Script rápido para limpiar espacio:

```bash
#!/bin/bash
# Script para limpiar espacio en disco

echo "=== Verificando espacio actual ==="
df -h /

echo "=== Limpiando Docker ==="
docker system prune -a -f --volumes
docker builder prune -a -f

echo "=== Limpiando sistema ==="
sudo apt clean
sudo apt autoremove -y

echo "=== Limpiando logs antiguos ==="
sudo journalctl --vacuum-time=3d

echo "=== Eliminando backups antiguos ==="
find /opt/tienda-online -name "backup_*.sql" -mtime +7 -delete 2>/dev/null

echo "=== Espacio disponible después de limpiar ==="
df -h /
```

## Apéndice: Cambiar Contraseñas de MySQL

### Cambiar contraseña de root de MySQL

**IMPORTANTE**: Si la base de datos ya tiene datos, usa el Método 1. Si es una instalación nueva, usa el Método 2.

**Método 1: Cambiar contraseña en base de datos existente (sin perder datos)**

```bash
# 1. Cambiar la contraseña dentro del contenedor MySQL
docker compose -f docker-compose.prod.yml exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} << EOF
ALTER USER 'root'@'%' IDENTIFIED BY 'TuNuevaContraseñaRoot123!';
ALTER USER 'root'@'localhost' IDENTIFIED BY 'TuNuevaContraseñaRoot123!';
FLUSH PRIVILEGES;
EOF

# 2. Verificar que el cambio funcionó
docker compose -f docker-compose.prod.yml exec db mysql -u root -pTuNuevaContraseñaRoot123! -e "SELECT 'Contraseña cambiada exitosamente' AS resultado;"

# 3. Actualizar el archivo .env con la nueva contraseña
nano .env
# Cambiar: MYSQL_ROOT_PASSWORD=contraseña_anterior
# Por: MYSQL_ROOT_PASSWORD=TuNuevaContraseñaRoot123!

# 4. Actualizar también la contraseña del usuario tienda_user (si es necesario)
docker compose -f docker-compose.prod.yml exec db mysql -u root -pTuNuevaContraseñaRoot123! << EOF
ALTER USER 'tienda_user'@'%' IDENTIFIED BY 'TuNuevaContraseñaUsuario123!';
FLUSH PRIVILEGES;
EOF

# 5. Actualizar MYSQL_PASSWORD en el archivo .env
nano .env
# Cambiar: MYSQL_PASSWORD=contraseña_anterior
# Por: MYSQL_PASSWORD=TuNuevaContraseñaUsuario123!

# 6. Reiniciar contenedores para aplicar cambios
docker compose -f docker-compose.prod.yml restart web db
```

**Método 2: Cambiar contraseña en instalación nueva (puede perder datos)**

```bash
# 1. Detener contenedores
docker compose -f docker-compose.prod.yml down

# 2. Eliminar volumen de base de datos (¡CUIDADO! Esto borra todos los datos)
docker volume rm tienda-online_db_data_prod

# 3. Editar .env con nuevas contraseñas
nano .env
# MYSQL_ROOT_PASSWORD=TuNuevaContraseñaRoot123!
# MYSQL_PASSWORD=TuNuevaContraseñaUsuario123!

# 4. Levantar contenedores (creará la BD con las nuevas contraseñas)
docker compose -f docker-compose.prod.yml up -d

# 5. Ejecutar migraciones nuevamente
docker compose -f docker-compose.prod.yml exec web php yii migrate --interactive=0
```

**Método 3: Cambiar solo la contraseña del usuario tienda_user**

```bash
# 1. Cambiar contraseña del usuario
docker compose -f docker-compose.prod.yml exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} << EOF
ALTER USER 'tienda_user'@'%' IDENTIFIED BY 'TuNuevaContraseñaUsuario123!';
FLUSH PRIVILEGES;
EOF

# 2. Actualizar .env
nano .env
# MYSQL_PASSWORD=TuNuevaContraseñaUsuario123!

# 3. Reiniciar contenedor web
docker compose -f docker-compose.prod.yml restart web
```

### Verificar usuarios y permisos de MySQL

```bash
# Ver todos los usuarios
docker compose -f docker-compose.prod.yml exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "SELECT user, host FROM mysql.user;"

# Ver permisos del usuario tienda_user
docker compose -f docker-compose.prod.yml exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "SHOW GRANTS FOR 'tienda_user'@'%';"
```

