# 🔧 Project Akhir - Troubleshooting Guide

## 🚨 Common Issues & Solutions

### Issue #1: "Port 8080 already in use"

**Error Message:**
```
Error response from daemon: driver failed programming external 
connectivity on endpoint project-akhir-webserver: 
Bind for 0.0.0.0:8080 failed: port is already allocated
```

**Causes:**
- Another application using port 8080
- Docker container already running

**Solutions:**

**Option A: Use Different Port**
1. Edit `docker-compose.yml`
2. Find line: `- "8080:80"`
3. Change to: `- "8089:80"` (or any available port)
4. Save and restart:
   ```powershell
   docker-compose restart
   ```
5. Access at: http://localhost:8089

**Option B: Kill Process Using Port**
```powershell
# Find process
netstat -ano | findstr :8080

# Kill process (replace PID)
taskkill /PID 1234 /F
```

**Option C: Check & Stop Old Container**
```powershell
# List all containers
docker ps -a

# Stop old container
docker stop project-akhir-webserver

# Remove it
docker rm project-akhir-webserver

# Start fresh
docker-compose up -d
```

---

### Issue #2: "Cannot connect to database"

**Error Message:**
```
SQLSTATE[HY000]: General error: 2002 Connection refused
Database connection error: Connection refused
```

**Causes:**
- MySQL container not ready yet
- Wrong credentials
- Network issues

**Solutions:**

**Step 1: Check Database Status**
```powershell
docker-compose ps
# Look for: project-akhir-db - should be "Up"
```

**Step 2: Wait for Database**
MySQL needs 60 seconds to start. Wait and retry.

**Step 3: Verify Credentials**
```powershell
# Check docker-compose.yml
Get-Content docker-compose.yml | Select-String "MYSQL"
```

Ensure in code:
```php
DB_HOST=db          // Not "localhost"
DB_USER=root
DB_PASSWORD=secret
DB_NAME=project_akhir
```

**Step 4: Check Database Logs**
```powershell
docker-compose logs db
```

**Step 5: Test Connection**
```powershell
docker exec -it project-akhir-db mysql -u root -psecret -h localhost
```

**Step 6: Force Restart**
```powershell
docker-compose down
docker-compose up -d
```

---

### Issue #3: "Docker daemon not running"

**Error Message:**
```
Cannot connect to the Docker daemon at unix:///var/run/docker.sock
error during connect: This error may indicate the daemon is not running
```

**Causes:**
- Docker Desktop not running
- Docker service stopped

**Solutions:**

**For Windows:**
1. Open **Docker Desktop** from Start Menu
2. Wait for "Docker is running" notification
3. Try command again

**For Linux:**
```bash
# Start Docker service
sudo systemctl start docker
sudo systemctl enable docker  # Auto-start on reboot

# Check status
sudo systemctl status docker
```

**For Mac:**
1. Open Applications > Docker
2. Wait for app to fully load
3. Check menu bar for Docker icon

---

### Issue #4: "Containers stuck on starting"

**Error:**
Containers keep restarting or stuck on startup

**Causes:**
- Configuration error
- Port conflicts
- Resource limitations

**Solutions:**

```powershell
# Check logs for specific errors
docker-compose logs --follow

# Restart specific service
docker-compose restart webserver

# Full reset
docker-compose down
docker-compose up -d --build
```

---

### Issue #5: "Permission denied errors"

**Error Message:**
```
Error: EACCES: permission denied, open '/var/www/html/...'
```

**Causes:**
- Running as non-root on Linux
- File ownership issues

**Solutions:**

**Windows:**
- Run PowerShell as Administrator
- Right-click PowerShell → "Run as administrator"

**Linux/Mac:**
```bash
# Fix file permissions
chmod -R 755 ./
chmod -R 644 ./*.*

# Or use sudo
sudo docker-compose up -d
```

---

### Issue #6: "Out of disk space"

**Error Message:**
```
No space left on device
Error: insufficient container processes available (1024 < 2048)
```

**Causes:**
- Docker images/containers taking space
- Database volume full

**Solutions:**

```powershell
# Check disk usage
docker system df

# Remove unused images
docker image prune -a

# Remove unused containers
docker container prune

# Remove unused volumes
docker volume prune

# Clean everything
docker system prune -a --volumes

# Reset database volume
docker-compose down -v
```

---

### Issue #7: "Web server shows blank page"

**Error:**
Browser shows white page or 500 error

**Causes:**
- PHP error
- Database connection failed
- Missing files

**Solutions:**

**Step 1: Check PHP Logs**
```powershell
docker-compose logs app
```

**Step 2: Check Nginx Logs**
```powershell
docker-compose logs webserver
```

**Step 3: Test Health Endpoint**
```powershell
curl http://localhost:8080/health.php
```

**Step 4: Access Container and Debug**
```powershell
docker exec -it project-akhir-app bash
cd /var/www/html
ls -la
```

**Step 5: Check Database Connection**
```powershell
docker exec -it project-akhir-db mysql -u root -psecret -e "SELECT 1"
```

---

### Issue #8: "Slow performance"

**Symptoms:**
- Slow page loads
- High CPU/memory usage

**Solutions:**

**Check Resource Usage:**
```powershell
docker stats
```

**Increase Container Resources:**
Edit `docker-compose.yml`:
```yaml
services:
  webserver:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 1G
```

**Optimize Database:**
```powershell
# Connect to MySQL
docker exec -it project-akhir-db mysql -u root -psecret project_akhir

# Check indexes
SHOW INDEX FROM table_name;

# Analyze query
EXPLAIN SELECT * FROM users;
```

---

### Issue #9: "Database corruption"

**Error:**
Database errors or corrupted tables

**Solutions:**

**Reset Database:**
```powershell
# Stop everything
docker-compose down

# Remove volume (WARNING: deletes database)
docker volume rm project-akhir_db_data

# Start fresh
docker-compose up -d

# Wait 60 seconds for initialization
```

**Backup Database First:**
```powershell
docker exec project-akhir-db mysqldump -u root -psecret project_akhir > backup.sql
```

**Restore from Backup:**
```powershell
docker exec -i project-akhir-db mysql -u root -psecret project_akhir < backup.sql
```

---

### Issue #10: "SSL certificate errors"

**Error:**
```
ERR_CERT_AUTHORITY_INVALID
SSL_ERROR_BAD_CERT_DOMAIN
```

**Causes:**
- Self-signed certificate
- Missing certificate
- Wrong domain

**Solutions:**

**For Development:**
- Use HTTP (not HTTPS)
- Browser will warn about self-signed cert
- Click "Proceed anyway" or "Advanced"

**For Production:**
```powershell
# Generate Let's Encrypt certificate
docker run -it --rm --name certbot \
  -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
  -v "/etc/letsencrypt:/etc/letsencrypt" \
  certbot/certbot certonly -d yourdomain.com
```

---

## 🔍 Diagnostic Commands

### Check Everything
```powershell
# Container status
docker-compose ps

# All logs
docker-compose logs --tail=50

# Resource usage
docker stats

# Network connectivity
docker network ls
docker network inspect project-akhir_project-network
```

### Test Connectivity
```powershell
# Test web server
curl -I http://localhost:8080

# Test database
docker exec project-akhir-db mysqladmin -u root -psecret ping

# Test health endpoint
curl http://localhost:8080/health.php

# Ping internal container
docker exec project-akhir-app ping db
```

### View Configuration
```powershell
# Docker compose config
docker-compose config

# Nginx config
docker exec project-akhir-webserver nginx -T

# PHP config
docker exec project-akhir-app php -i

# MySQL config
docker exec project-akhir-db mysqld --print-defaults
```

---

## 🛠️ Recovery Procedures

### Complete Fresh Start
```powershell
# Stop everything
docker-compose down

# Remove all volumes
docker-compose down -v

# Remove stopped containers
docker container prune -f

# Remove all stopped images
docker image prune -f

# Remove dangling volumes
docker volume prune -f

# Start fresh
docker-compose up -d
```

### Rebuild Containers
```powershell
# Rebuild PHP container
docker-compose build --no-cache app

# Rebuild all
docker-compose build --no-cache

# Start
docker-compose up -d
```

### Rollback to Last Known Good
```powershell
# Check git history
git log --oneline

# Restore previous version
git checkout <commit-hash>

# Restart containers
docker-compose up -d --build
```

---

## 📋 Health Check Verification

### Verify All Services Healthy
```powershell
$services = @('webserver', 'app', 'db', 'phpmyadmin')

foreach ($service in $services) {
    $health = docker-compose ps | Select-String $service
    if ($health -match "healthy|Up") {
        Write-Host "✓ $service: OK" -ForegroundColor Green
    } else {
        Write-Host "✗ $service: ISSUE" -ForegroundColor Red
    }
}
```

### Check Specific Service
```powershell
# Detailed info
docker-compose ps project-akhir-webserver

# Inspect container
docker inspect project-akhir-webserver

# View logs
docker logs -f project-akhir-webserver
```

---

## 🚨 Emergency Procedures

### Container Crashing Repeatedly
```powershell
# 1. Stop all
docker-compose stop

# 2. Check logs for errors
docker-compose logs

# 3. Fix issue (edit docker-compose.yml or .env)

# 4. Restart
docker-compose up -d
```

### Database Locked
```powershell
# 1. Check processes
docker exec project-akhir-db mysql -u root -psecret -e "SHOW PROCESSLIST;"

# 2. Kill process
docker exec project-akhir-db mysql -u root -psecret -e "KILL 123;"

# 3. If stuck, restart container
docker-compose restart db
```

### Out of Memory
```powershell
# 1. Check usage
docker stats

# 2. Stop non-critical containers
docker-compose stop portainer

# 3. Increase limits or restart
docker-compose restart
```

---

## 📚 Log File Locations

Inside containers:
```
PHP Errors:     /var/log/php-error.log
Nginx Access:   /var/log/nginx/access.log
Nginx Errors:   /var/log/nginx/error.log
MySQL:          /var/log/mysql/
```

View logs:
```powershell
# All logs
docker-compose logs

# Specific service
docker-compose logs app

# Follow in real-time
docker-compose logs -f

# Last 100 lines
docker-compose logs --tail=100

# Specific time range
docker-compose logs --since 2025-12-01 --until 2025-12-02
```

---

## 🆘 When All Else Fails

### Nuclear Option (Complete Reset)
```powershell
# WARNING: This deletes everything related to this project!

# 1. Stop all containers
docker-compose down -v

# 2. Remove all project data
docker container rm project-akhir-*
docker volume rm project-akhir_*

# 3. Remove images (optional)
docker image rm project-akhir-app php:8.1-apache nginx:alpine

# 4. Prune system
docker system prune -a -f

# 5. Start fresh
docker-compose pull
docker-compose up -d
```

### Get Help
1. Check logs: `docker-compose logs`
2. Check health: http://localhost:8080/health.php
3. Review HOW_TO_ACCESS.md
4. Search issue on Docker docs
5. Check container resources: `docker stats`

---

## ✅ Post-Fix Verification

After fixing an issue:

```powershell
# 1. Check all containers running
docker-compose ps

# 2. Verify each service
curl http://localhost:8080           # Main app
curl http://localhost:8081           # phpMyAdmin
curl http://localhost:9000           # Portainer

# 3. Check database
docker exec project-akhir-db mysqladmin -u root -psecret ping

# 4. View recent logs
docker-compose logs --tail=20

# 5. Monitor for issues
docker stats
```

---

## 📞 Support Resources

- **Docker Troubleshooting:** https://docs.docker.com/config/containers/troubleshoot/
- **Docker Compose Issues:** https://docs.docker.com/compose/gettingstarted/#common-issues
- **MySQL Error Codes:** https://dev.mysql.com/doc/
- **Nginx Docs:** https://nginx.org/en/docs/
- **PHP Errors:** https://www.php.net/manual/en/errorfunc.constants.php

---

**Last Updated:** December 2025
**Version:** 1.0
**Status:** ✅ Comprehensive Troubleshooting Guide
