#!/bin/sh
set -e

# Establecer permisos básicos
chmod -R 777 /var/www/html/runtime 2>/dev/null || true
chmod -R 777 /var/www/html/web/uploads 2>/dev/null || true
chmod -R 777 /var/www/html/web/assets 2>/dev/null || true

# Configurar PHP-FPM para pasar variables de entorno a PHP
# Esto es crítico para que getenv() funcione correctamente en PHP-FPM
if [ -f /usr/local/etc/php-fpm.d/www.conf ]; then
    # Configurar clear_env = no
    if ! grep -q "^clear_env" /usr/local/etc/php-fpm.d/www.conf; then
        sed -i '/\[www\]/a clear_env = no' /usr/local/etc/php-fpm.d/www.conf
        echo "Configurado clear_env = no en PHP-FPM"
    elif grep -q "^clear_env = yes" /usr/local/etc/php-fpm.d/www.conf; then
        sed -i 's/^clear_env = yes/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf
        echo "Cambiado clear_env a no en PHP-FPM"
    fi
    
    # Configurar variables de entorno explícitamente usando un bloque temporal
    # Eliminar configuraciones anteriores de estas variables si existen
    sed -i '/^env\[MYSQL_HOST\]/d' /usr/local/etc/php-fpm.d/www.conf
    sed -i '/^env\[MYSQL_DATABASE\]/d' /usr/local/etc/php-fpm.d/www.conf
    sed -i '/^env\[MYSQL_USER\]/d' /usr/local/etc/php-fpm.d/www.conf
    sed -i '/^env\[MYSQL_PASSWORD\]/d' /usr/local/etc/php-fpm.d/www.conf
    sed -i '/^env\[MYSQL_ROOT_PASSWORD\]/d' /usr/local/etc/php-fpm.d/www.conf
    
    # Crear un archivo temporal con las variables expandidas
    {
        echo ""
        echo "; Variables de entorno MySQL configuradas automáticamente"
        [ -n "${MYSQL_HOST}" ] && echo "env[MYSQL_HOST] = ${MYSQL_HOST}"
        [ -n "${MYSQL_DATABASE}" ] && echo "env[MYSQL_DATABASE] = ${MYSQL_DATABASE}"
        [ -n "${MYSQL_USER}" ] && echo "env[MYSQL_USER] = ${MYSQL_USER}"
        [ -n "${MYSQL_PASSWORD}" ] && echo "env[MYSQL_PASSWORD] = ${MYSQL_PASSWORD}"
        [ -n "${MYSQL_ROOT_PASSWORD}" ] && echo "env[MYSQL_ROOT_PASSWORD] = ${MYSQL_ROOT_PASSWORD}"
    } >> /usr/local/etc/php-fpm.d/www.conf
    
    echo "Variables de entorno configuradas en PHP-FPM"
fi

# Esperar a que MySQL esté listo (solo si MYSQL_ROOT_PASSWORD está definido)
if [ -n "${MYSQL_ROOT_PASSWORD}" ]; then
    echo "Esperando a que MySQL esté disponible..."
    MYSQL_HOST=${MYSQL_HOST:-db}
    MYSQL_USER=${MYSQL_USER:-root}
    MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD}
    
    MAX_ATTEMPTS=30
    ATTEMPT=0
    
    until mysqladmin ping -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" --silent 2>/dev/null; do
        ATTEMPT=$((ATTEMPT + 1))
        if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
            echo "Advertencia: MySQL no está disponible después de $MAX_ATTEMPTS intentos. Continuando..."
            break
        fi
        echo "Esperando MySQL... (intento $ATTEMPT/$MAX_ATTEMPTS)"
        sleep 2
    done
    
    if [ $ATTEMPT -lt $MAX_ATTEMPTS ]; then
        echo "MySQL está disponible!"
    fi
else
    echo "MYSQL_ROOT_PASSWORD no está definido. Saltando verificación de MySQL..."
fi

# Ejecutar comando original
exec "$@"

