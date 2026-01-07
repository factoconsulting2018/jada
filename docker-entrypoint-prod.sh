#!/bin/sh
set -e

# Establecer permisos básicos
chmod -R 777 /var/www/html/runtime 2>/dev/null || true
chmod -R 777 /var/www/html/web/uploads 2>/dev/null || true
chmod -R 777 /var/www/html/web/assets 2>/dev/null || true

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

