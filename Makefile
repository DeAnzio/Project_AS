.PHONY: help up down rebuild logs shell-app shell-db status clean test

help:
	@echo "Project Akhir - Docker Commands"
	@echo "=================================="
	@echo "make up        - Start containers"
	@echo "make down      - Stop containers"
	@echo "make rebuild   - Rebuild and start containers"
	@echo "make logs      - View container logs"
	@echo "make status    - Show container status"
	@echo "make shell-app - Access PHP container shell"
	@echo "make shell-db  - Access MySQL container shell"
	@echo "make clean     - Remove volumes and reset database"
	@echo "make test      - Run basic tests"

up:
	docker-compose up -d
	@echo "✓ Containers started"
	@echo "  Main App: http://localhost:8080"
	@echo "  phpMyAdmin: http://localhost:8081"

down:
	docker-compose down
	@echo "✓ Containers stopped"

rebuild:
	docker-compose up -d --build
	@echo "✓ Containers rebuilt and started"

logs:
	docker-compose logs -f

status:
	docker-compose ps
	@echo ""
	@echo "Recent logs (last 10 lines):"
	docker-compose logs --tail=10

shell-app:
	docker exec -it project-akhir-app bash

shell-db:
	docker exec -it project-akhir-db bash

clean:
	docker-compose down -v
	@echo "✓ Volumes removed - database reset"

test:
	@echo "Testing connectivity..."
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:8080
	@echo "✓ Web server responding"

install-db:
	docker exec project-akhir-db mysql -u root -psecret project_akhir < docker/mysql/init.sql
	@echo "✓ Database initialized"

info:
	@echo "Project Akhir Information"
	@echo "=============================="
	docker-compose config
