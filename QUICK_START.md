# 🎯 Project Akhir - Quick Reference Card

## 🚀 Launch (Copy & Paste)

```powershell
cd d:\project-akhir
docker-compose up -d
start http://localhost:8080
```

## 🌐 Access Points

| Service | URL | Use |
|---------|-----|-----|
| **App** | http://localhost:8080 | Main application |
| **Database GUI** | http://localhost:8081 | phpMyAdmin |
| **Container Mgmt** | http://localhost:9000 | Portainer |
| **Health Check** | http://localhost:8080/health.php | Monitor status |

## 🛑 Stop Application

```powershell
docker-compose down
```

## 📋 Check Status

```powershell
docker-compose ps
```

## 📊 View Logs

```powershell
docker-compose logs -f
```

## 🔧 Common Issues & Solutions

### ❌ Port 8080 in use
**Fix:** Change port in `docker-compose.yml` (line 24)
```yaml
ports:
  - "8089:80"  # Use 8089 instead
```

### ❌ Database not ready
**Wait:** 60 seconds, then refresh browser

### ❌ Docker not running
**Fix:** Open Docker Desktop app

### ❌ Permission denied
**Fix:** Run PowerShell as Administrator

## 💾 Database Access

```powershell
# Via GUI: http://localhost:8081
# Host: db
# User: root  
# Pass: secret

# Via Command Line:
docker exec -it project-akhir-db mysql -u root -psecret project_akhir
```

## 🔑 Important Files

| File | Purpose |
|------|---------|
| `HOW_TO_ACCESS.md` | Complete setup guide |
| `README-SETUP.md` | Quick reference |
| `DEPLOYMENT.md` | Production guide |
| `.env` | Environment config |
| `docker-compose.yml` | Container config |

## 📂 Project Folders

```
index.php          ← Entry point (redirects to login)
auth/              ← Login/Register
dashboard/         ← Dashboard pages
config/            ← App & Database config
docker/            ← Docker config files
includes/          ← Shared files
```

## 🧪 Test Connectivity

```powershell
# Test web server
curl http://localhost:8080

# Test database
docker exec project-akhir-db mysqladmin -u root -psecret ping

# Test health
curl http://localhost:8080/health.php
```

## 🐚 Access Container Shells

```powershell
# PHP Container
docker exec -it project-akhir-app bash

# MySQL Container  
docker exec -it project-akhir-db bash

# Nginx Container
docker exec -it project-akhir-webserver sh
```

## 🔄 Restart Services

```powershell
# Restart all
docker-compose restart

# Restart specific service
docker-compose restart webserver
```

## 🧹 Clean Up

```powershell
# Stop & remove containers
docker-compose down

# Remove volumes (DELETE DATABASE!)
docker-compose down -v

# Prune unused Docker resources
docker system prune -a
```

## 🏗️ Rebuild After Changes

```powershell
docker-compose up -d --build
```

## 📈 Monitor Resources

```powershell
docker stats
```

## 📝 View Config

```powershell
# Show docker-compose config
docker-compose config

# Show environment
Get-Content .env
```

## 🔐 Default Credentials

```
MySQL Root:     root / secret
App Database:   project_user / userpass  
Database Name:  project_akhir
```

## 🚨 Emergency Restart

```powershell
docker-compose down
docker-compose up -d
```

## 📞 Need Help?

**Check these files in order:**
1. This file (Quick Reference)
2. `HOW_TO_ACCESS.md` (Setup guide)
3. `README-SETUP.md` (Troubleshooting)
4. `DEPLOYMENT.md` (Production)

## 🎉 You're Good to Go!

Your application is ready at: **http://localhost:8080**

---

**Saved:** December 2025 | **Status:** ✅ Ready to Use
