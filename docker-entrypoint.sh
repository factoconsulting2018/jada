#!/bin/bash
set -e

# Establecer permisos básicos
chmod -R 777 /var/www/html/runtime 2>/dev/null || true
chmod -R 777 /var/www/html/web/uploads 2>/dev/null || true

# Ejecutar comando original
exec "$@"

