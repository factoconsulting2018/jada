.PHONY: help build up down restart logs shell migrate composer-install clean

help: ## Mostrar esta ayuda
	@echo "Comandos disponibles:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Desarrollo
build: ## Construir imágenes Docker
	docker-compose build

up: ## Iniciar contenedores
	docker-compose up -d
	@echo "Esperando a que los servicios estén listos..."
	sleep 5
	@echo "Servicios iniciados. Accede a http://localhost:8081"

down: ## Detener contenedores
	docker-compose down

restart: ## Reiniciar contenedores
	docker-compose restart

logs: ## Ver logs
	docker-compose logs -f

shell: ## Acceder al contenedor web
	docker-compose exec web bash

migrate: ## Ejecutar migraciones
	docker-compose exec web php yii migrate

composer-install: ## Instalar dependencias de Composer
	docker-compose exec web composer install

init: ## Inicializar proyecto (primera vez)
	@echo "Configurando base de datos..."
	cp config/db-docker.php config/db.php
	@echo "Construyendo imágenes..."
	docker-compose build
	@echo "Iniciando contenedores..."
	docker-compose up -d
	@echo "Esperando a que MySQL esté listo..."
	sleep 10
	@echo "Instalando dependencias..."
	docker-compose exec web composer install
	@echo "Ejecutando migraciones..."
	docker-compose exec web php yii migrate
	@echo "¡Listo! Accede a http://localhost:8081"

clean: ## Limpiar contenedores y volúmenes
	docker-compose down -v
	docker system prune -f

# Producción
build-prod: ## Construir imágenes para producción
	docker-compose -f docker-compose.prod.yml build

up-prod: ## Iniciar contenedores de producción
	docker-compose -f docker-compose.prod.yml up -d

down-prod: ## Detener contenedores de producción
	docker-compose -f docker-compose.prod.yml down

logs-prod: ## Ver logs de producción
	docker-compose -f docker-compose.prod.yml logs -f

migrate-prod: ## Ejecutar migraciones en producción
	docker-compose -f docker-compose.prod.yml exec web php yii migrate

backup-db: ## Hacer backup de la base de datos
	docker-compose exec db mysqldump -u root -proot_password tienda_online > backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "Backup creado"

backup-db-prod: ## Hacer backup de la base de datos en producción
	docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE} > backup_prod_$$(date +%Y%m%d_%H%M%S).sql
	@echo "Backup de producción creado"

