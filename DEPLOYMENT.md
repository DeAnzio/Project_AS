# Production Deployment Checklist

## 🔐 Security Checklist

### Immediate Actions
- [ ] Change database passwords in `docker-compose.yml`
- [ ] Update `.env` with production values
- [ ] Generate SSL certificates in `docker/nginx/ssl/`
- [ ] Update `APP_URL` in `.env` to production domain
- [ ] Change `APP_DEBUG` to `false`

### Database Security
- [ ] Use strong password for MySQL root user
- [ ] Create separate database user (non-root)
- [ ] Backup database regularly
- [ ] Use encrypted connections for remote DB

### Application Security
- [ ] Enable HTTPS only
- [ ] Set secure session cookies
- [ ] Implement rate limiting
- [ ] Add CORS headers if needed
- [ ] Setup firewall rules

### Docker Security
- [ ] Use specific image versions (not 'latest')
- [ ] Run containers as non-root user
- [ ] Use read-only filesystems where possible
- [ ] Implement network policies
- [ ] Regular image security updates

---

## 📦 Production Files to Modify

### 1. docker-compose.yml
```yaml
# Change restart policy
restart: on-failure:5

# Add resource limits
deploy:
  resources:
    limits:
      cpus: '1'
      memory: 512M
    reservations:
      cpus: '0.5'
      memory: 256M
```

### 2. .env (Production)
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_HOST=db
MYSQL_ROOT_PASSWORD=strong_password_here
SESSION_LIFETIME=1440
```

### 3. docker/nginx/nginx.conf
Add HTTPS configuration and redirect HTTP to HTTPS

### 4. docker/php/conf.d/php.ini
Ensure production-grade settings:
```ini
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php-error.log
```

---

## 🚀 Deployment Steps

### Step 1: Prepare Server
```bash
# Update system
sudo apt update && apt upgrade -y

# Install Docker & Docker Compose
sudo curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group
sudo usermod -aG docker $USER
```

### Step 2: Deploy Application
```bash
# Clone repository
git clone <your-repo> project-akhir
cd project-akhir

# Setup environment
cp .env.example .env
# Edit .env with production values

# Start containers
docker-compose up -d
```

### Step 3: Setup SSL (Let's Encrypt)
```bash
# Use certbot with Docker
docker run -it --rm --name certbot \
  -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
  -v "/etc/letsencrypt:/etc/letsencrypt" \
  -v "/var/log/letsencrypt:/var/log/letsencrypt" \
  certbot/certbot certonly -d yourdomain.com
```

### Step 4: Configure Nginx for SSL
Update `docker/nginx/nginx.conf` to use SSL certificates

### Step 5: Setup Monitoring
```bash
# Enable container health checks
docker-compose up -d

# Setup log rotation
sudo logrotate -d /etc/logrotate.d/docker
```

---

## 📊 Monitoring & Maintenance

### Backup Strategy
```bash
# Daily database backup
docker exec project-akhir-db mysqldump -u root -p$MYSQL_ROOT_PASSWORD project_akhir > backup_$(date +%Y%m%d).sql

# Weekly full backup
```

### Log Management
```bash
# View production logs
docker-compose logs --follow --tail=100

# Store logs externally
docker-compose logs > app_logs_$(date +%Y%m%d).log
```

### Updates
```bash
# Pull latest images
docker-compose pull

# Rebuild and restart
docker-compose up -d --build
```

---

## 🔍 Performance Optimization

### Database
- Add indexes on frequently queried columns
- Optimize queries
- Enable query caching

### Web Server
- Enable gzip compression
- Browser caching headers
- Static file caching

### Application
- Implement opcode caching (PHP OPcache)
- Use CDN for static assets
- Implement proper error handling

---

## ⚠️ Common Issues in Production

### Issue: Database slow performance
**Solution:**
- Increase MySQL max_connections
- Add proper indexes
- Optimize queries

### Issue: Out of memory
**Solution:**
- Increase container memory limit
- Implement pagination
- Use lazy loading

### Issue: Port conflicts
**Solution:**
- Use reverse proxy (nginx)
- Change port mappings
- Use different domains

---

## 📋 Post-Deployment

- [ ] Test all features thoroughly
- [ ] Monitor logs for errors
- [ ] Check resource usage
- [ ] Verify backups working
- [ ] Setup alerting system
- [ ] Document production setup
- [ ] Train team on deployment

---

## 🔗 Resources

- Docker Docs: https://docs.docker.com
- Nginx Docs: https://nginx.org/en/docs/
- PHP Security: https://www.php.net/manual/en/security.php
- Let's Encrypt: https://letsencrypt.org/

---

**Status:** Ready for Production Deployment
**Last Updated:** December 2025
