# 🏗️ Project Akhir - Architecture & Infrastructure

## 📐 System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT TIER (Browser)                    │
│                                                              │
│  http://localhost:8080      http://localhost:8081/9000     │
│  (Main App)                 (Tools)                          │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│              PRESENTATION TIER (Nginx + PHP)                 │
│                                                              │
│  ┌──────────────────────────────────────────────────┐      │
│  │  Nginx Web Server (Docker Container)             │      │
│  │  - Port: 8080, 8443                              │      │
│  │  - Health: Checks every 30s                      │      │
│  │  - Role: Reverse proxy, static file serving      │      │
│  └──────────────────────┬───────────────────────────┘      │
│                         │ (FastCGI)                         │
│  ┌──────────────────────▼───────────────────────────┐      │
│  │  PHP-FPM Container                               │      │
│  │  - Version: PHP 8.1                              │      │
│  │  - Extensions: PDO, MySQL, GD, Zip               │      │
│  │  - Volume: Project source code                   │      │
│  └──────────────────────┬───────────────────────────┘      │
└────────────────────────┬────────────────────────────────────┘
                         │ (PDO Protocol)
┌────────────────────────▼────────────────────────────────────┐
│              APPLICATION TIER (Business Logic)               │
│                                                              │
│  Authentication   Dashboard    Database Ops                │
│  └─ Login        └─ Admin      └─ CRUD                     │
│  └─ Register     └─ Manager    └─ Queries                  │
│  └─ Logout       └─ User                                   │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│               DATA TIER (MySQL Database)                     │
│                                                              │
│  ┌──────────────────────────────────────────────────┐      │
│  │  MySQL 8.0 Container                            │      │
│  │  - Port: 3306 (exposed for external access)      │      │
│  │  - Database: project_akhir                       │      │
│  │  - Health: Checks every 10s                      │      │
│  │  - Storage: Docker volume (persistent)           │      │
│  └──────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│            SUPPORT SERVICES (Docker Containers)              │
│                                                              │
│  ┌──────────────────┐  ┌──────────────────────────────┐   │
│  │  phpMyAdmin      │  │  Portainer                   │   │
│  │  (Database GUI)  │  │  (Container Management)      │   │
│  │  Port: 8081      │  │  Port: 9000                  │   │
│  └──────────────────┘  └──────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## 🐳 Docker Container Details

### 1. **project-akhir-app** (PHP Application)
- **Image:** php:8.1-apache
- **Role:** Run PHP application logic
- **Volume:** `./` → `/var/www/html`
- **Network:** project-network
- **Dependencies:** db (MySQL)
- **Health:** No specific check (always on)

### 2. **project-akhir-webserver** (Nginx)
- **Image:** nginx:alpine
- **Role:** Web server, reverse proxy
- **Ports:** 
  - 8080:80 (HTTP)
  - 8443:443 (HTTPS)
- **Health Check:** HTTP requests every 30s
- **Dependencies:** app (PHP)
- **Config:** ./docker/nginx/nginx.conf

### 3. **project-akhir-db** (MySQL)
- **Image:** mysql:8.0
- **Role:** Store application data
- **Port:** 3306:3306
- **Health Check:** mysqladmin ping every 10s
- **Volume:** db_data (persistent storage)
- **Init Script:** ./docker/mysql/init.sql
- **Environment:**
  - ROOT_PASSWORD: secret
  - DATABASE: project_akhir
  - USER: project_user

### 4. **project-akhir-pma** (phpMyAdmin)
- **Image:** phpmyadmin:latest
- **Role:** Database management GUI
- **Port:** 8081:80
- **Dependencies:** db (MySQL)
- **Use:** http://localhost:8081

### 5. **project-akhir-portainer** (Portainer)
- **Image:** portainer/portainer-ce:latest
- **Role:** Docker container management UI
- **Port:** 9000:9000
- **Use:** http://localhost:9000

## 🌐 Network Architecture

```
Docker Network: project-network (bridge)

┌────────────────────────────────────────┐
│   project-network (bridge mode)        │
│                                        │
│  Container IPs (auto-assigned):       │
│                                        │
│  ├─ app:9000 (PHP-FPM)               │
│  ├─ webserver:80 (Nginx)             │
│  ├─ db:3306 (MySQL)                  │
│  ├─ phpmyadmin:80 (phpMyAdmin)        │
│  └─ portainer:9000 (Portainer)        │
│                                        │
│  Internal DNS:                         │
│  ├─ app → project-akhir-app           │
│  ├─ db → project-akhir-db             │
│  └─ phpmyadmin → project-akhir-pma    │
└────────────────────────────────────────┘
     ↕ Port Mapping ↕
┌────────────────────────────────────────┐
│   Host Machine (localhost)              │
│                                        │
│  :8080 → webserver:80                 │
│  :8443 → webserver:443                │
│  :3306 → db:3306                      │
│  :8081 → phpmyadmin:80                │
│  :9000 → portainer:9000               │
└────────────────────────────────────────┘
```

## 💾 Data Persistence

### Volumes
```
volumes:
  db_data:          # MySQL data persistence
    driver: local
    mount: /var/lib/mysql
  
  portainer_data:   # Portainer configuration
    driver: local
    mount: /data
```

### Bind Mounts
```
./                  → /var/www/html      (Application code)
./docker/php/       → /usr/local/etc/    (PHP configuration)
./docker/nginx/     → /etc/nginx/        (Nginx configuration)
./docker/mysql/     → /etc/mysql/        (MySQL configuration)
```

## 🔄 Request Flow

```
1. User Browser
   ↓ (HTTP GET /dashboard)
   
2. Nginx (Port 8080)
   ├─ Check if static file → Serve directly
   ├─ If PHP file → Forward to PHP-FPM
   ↓
   
3. PHP-FPM Application
   ├─ Parse request
   ├─ Load config/database.php
   ├─ Query database
   ↓
   
4. MySQL Database
   ├─ Execute query
   ├─ Return result
   ↓
   
5. PHP Application
   ├─ Process result
   ├─ Render HTML
   ↓
   
6. Nginx
   ├─ Add headers (CORS, security)
   ├─ Compress response
   ↓
   
7. Browser
   ↓ (Display page)
   
8. Complete
```

## 🏥 Health Check Strategy

### Nginx Health Check
```
Endpoint: http://localhost/
Interval: 30 seconds
Timeout: 10 seconds
Retries: 3 attempts
Start Period: 40 seconds
Status: healthy / unhealthy
```

### MySQL Health Check
```
Command: mysqladmin ping
Interval: 10 seconds
Timeout: 5 seconds
Retries: 5 attempts
Start Period: 60 seconds (initial wait)
Status: healthy / unhealthy
```

### Application Health Check
```
Endpoint: /health.php
Returns: JSON with status
Checks: PHP version, DB connection, extensions
```

## 🔒 Security Architecture

### Network Isolation
- Containers use isolated network (project-network)
- Only Nginx exposed to host ports
- PHP/MySQL only accessible within network

### Access Control
- Nginx security headers (CORS, CSP, X-Frame-Options)
- Database user isolation
- Session management

### Data Protection
- Database volume encryption-ready
- Environment variables for secrets
- SSL certificate support

## 📊 Performance Considerations

### Caching Strategy
```
Static Files → Browser Cache (1 year)
Database Queries → PHP in-memory
Session Data → Database
```

### Resource Limits
```
CPU: Configurable per container
Memory: 512MB default limit
Disk: Volume-based storage
```

## 🚀 Scaling Considerations

### Horizontal Scaling
- Multiple PHP containers behind load balancer
- Shared database
- Distributed session storage

### Vertical Scaling
- Increase container resource limits
- Database optimization
- Cache layer (Redis)

## 🔧 Configuration Management

### Environment Variables
```
Loaded from: .env file
Used in: docker-compose.yml
Access: getenv() in PHP
```

### Configuration Files
```
PHP Config:     docker/php/conf.d/php.ini
Nginx Config:   docker/nginx/nginx.conf
MySQL Config:   docker/mysql/my.cnf
App Config:     config/config.php
```

## 🛠️ Development Workflow

```
Code Change
    ↓
Save file (hot reload via volume)
    ↓
Browser refresh
    ↓
Nginx serves updated file
    ↓
PHP processes request
    ↓
Database returns result
    ↓
Browser displays update
```

## 📈 Monitoring & Logging

### Container Logs
- Access: `docker-compose logs -f`
- Each container maintains its own log stream

### Health Status
- View: `docker-compose ps`
- Check: `/health.php` endpoint

### Performance Monitoring
- CPU/Memory: `docker stats`
- Portainer UI: http://localhost:9000

## 🌳 Directory Structure Inside Containers

### PHP Container (`/var/www/html`)
```
/var/www/html/
├── index.php
├── auth/
├── config/
├── dashboard/
├── docker/
├── includes/
├── assets/
└── database/
```

### Nginx Container (`/etc/nginx`)
```
/etc/nginx/
├── conf.d/
│   └── default.conf (from ./docker/nginx/nginx.conf)
├── nginx.conf
└── ssl/ (for certificates)
```

### MySQL Container (`/var/lib/mysql`)
```
/var/lib/mysql/
├── project_akhir/ (database directory)
├── mysql/ (system database)
└── performance_schema/
```

## 🔄 Dependency Management

```
webserver (Nginx)
    ↓ depends_on
    app (PHP)
        ↓ depends_on
        db (MySQL)

phpmyadmin
    ↓ depends_on
    db (MySQL)

portainer
    (No dependencies)
```

## 📝 Startup Sequence

```
1. docker-compose up -d
2. Create network (project-network)
3. Start MySQL (db) - waits 60s for readiness
4. Start PHP (app) - waits for db connection
5. Start Nginx (webserver) - waits for app
6. Start phpMyAdmin - waits for db
7. Start Portainer - independent
8. All services ready (~ 70 seconds)
```

---

## 📞 Architecture Review Checklist

- ✅ Multi-tier architecture (Presentation, Application, Data)
- ✅ Container isolation and networking
- ✅ Health monitoring and automatic restarts
- ✅ Data persistence with volumes
- ✅ Security considerations
- ✅ Scalability ready
- ✅ Development-friendly hot reload
- ✅ Production-grade configuration

---

**Last Updated:** December 2025
**Architecture Status:** ✅ Production Ready
