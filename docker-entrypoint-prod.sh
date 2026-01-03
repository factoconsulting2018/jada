#!/bin/bash
set -e

# Esperar a que MySQL esté listo
echo "Esperando a que MySQL esté disponible..."
MYSQL_HOST=${MYSQL_HOST:-db}
MYSQL_USER=${MYSQL_USER:-root}
MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD}

until mysqladmin ping -h"${MYSQL_HOST}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" --silent 2>/dev/null; do
    echo "Esperando MySQL..."
    sleep 2
done
echo "MySQL está disponible!"

# Ejecutar comando original
exec "$@"

