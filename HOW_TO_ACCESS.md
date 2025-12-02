# 🚀 Cara Mengakses Web App - Project Akhir

## ⚡ Quick Start (3 Steps)

### Step 1: Buka PowerShell/Terminal
Navigasi ke folder project:
```powershell
cd d:\project-akhir
```

### Step 2: Jalankan Docker
```powershell
docker-compose up -d
```

### Step 3: Buka di Browser
```
http://localhost:8080
```

---

## 🌐 Akses Point Lengkap

| Service | URL | Fungsi |
|---------|-----|--------|
| **Main App** | http://localhost:8080 | Aplikasi utama |
| **phpMyAdmin** | http://localhost:8081 | Manage database |
| **Portainer** | http://localhost:9000 | Container management |
| **Health Check** | http://localhost:8080/health.php | Monitor kesehatan app |

---

## 📋 Langkah Detail

### 1️⃣ Pastikan Docker Desktop Running
**Windows:**
- Buka Docker Desktop dari Start Menu
- Tunggu sampai "Docker is running" di system tray
- Verifikasi:
  ```powershell
  docker --version
  docker-compose --version
  ```

### 2️⃣ Start Containers
```powershell
cd d:\project-akhir
docker-compose up -d
```

**Output yang diharapkan:**
```
Creating project-akhir-db ...
Creating project-akhir-app ...
Creating project-akhir-webserver ...
Creating project-akhir-pma ...
Creating project-akhir-portainer ...
```

### 3️⃣ Tunggu Containers Siap
Pertama kali mungkin membutuhkan 60 detik untuk database siap. Cek status:

```powershell
docker-compose ps
```

Status harus `healthy` atau `Up`:
```
NAME                       STATUS              PORTS
project-akhir-webserver    Up (healthy)        0.0.0.0:8080->80/tcp
project-akhir-app          Up                  
project-akhir-db           Up (healthy)        0.0.0.0:3306->3306/tcp
project-akhir-pma          Up                  0.0.0.0:8081->80/tcp
project-akhir-portainer    Up                  0.0.0.0:9000->9000/tcp
```

### 4️⃣ Akses Aplikasi
Buka browser dan kunjungi:
- **Aplikasi:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **Portainer:** http://localhost:9000

---

## 🔧 Troubleshooting

### ❌ "Port already in use"
Jika port 8080 sudah terpakai:

**Windows PowerShell - Cari port yang sudah terpakai:**
```powershell
netstat -ano | findstr :8080
```

**Solusi:** Ubah port di `docker-compose.yml`
```yaml
ports:
  - "8089:80"  # Ganti 8080 menjadi 8089
```

Lalu akses: http://localhost:8089

### ❌ "Cannot connect to database"
Tunggu 60 detik sampai MySQL siap. Cek log:
```powershell
docker-compose logs db
```

### ❌ "Connection refused"
Pastikan Docker containers running:
```powershell
docker-compose ps
```

Restart jika ada yang tidak running:
```powershell
docker-compose restart
```

### ❌ "Permission denied"
Jalankan PowerShell sebagai Administrator:
- Right-click PowerShell → "Run as administrator"
- Coba lagi

---

## 📊 Monitoring & Debugging

### View Real-time Logs
```powershell
# Semua logs
docker-compose logs -f

# Log dari service tertentu
docker-compose logs -f webserver
docker-compose logs -f app
docker-compose logs -f db
```

### Check Container Status
```powershell
docker-compose ps
```

### View Resource Usage
```powershell
docker stats
```

### Access Container Shell
```powershell
# PHP Container
docker exec -it project-akhir-app bash

# MySQL Container
docker exec -it project-akhir-db bash
```

---

## 🛠️ Useful Commands

### Stop Containers
```powershell
docker-compose down
```

### Stop & Clear Everything
```powershell
docker-compose down -v
```
⚠️ **Warning:** Ini akan menghapus database!

### Rebuild Containers
```powershell
docker-compose up -d --build
```

### View Detailed Logs
```powershell
docker-compose logs --follow --tail=100
```

---

## 🐘 Database Access

### Via phpMyAdmin (GUI)
1. Buka http://localhost:8081
2. Login dengan:
   - **Server:** db
   - **Username:** root
   - **Password:** secret

### Via Command Line
```powershell
docker exec -it project-akhir-db mysql -u root -psecret project_akhir
```

### Connection Details
| Setting | Value |
|---------|-------|
| Host | localhost:3306 |
| Database | project_akhir |
| Username | root |
| Password | secret |

---

## 📁 Project Structure Overview

```
project-akhir/
├── index.php              # Entry point
├── auth/                  # Authentication
│   ├── login.php
│   ├── register.php
│   └── process_login.php
├── dashboard/             # Dashboard pages
├── config/                # Config files
│   ├── config.php
│   └── database.php
├── database/              # DB setup
│   └── setup.sql
├── docker/                # Docker config
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── includes/              # Shared files
├── assets/                # CSS, JS, images
├── docker-compose.yml     # Container config
└── Dockerfile            # PHP image config
```

---

## 🔒 Security Notes

- Environment variables ada di `.env`
- Database password di `docker-compose.yml` (ubah untuk production)
- SSL certificate di `docker/nginx/ssl/`
- Sensitive files di-exclude dari public access

---

## ✅ Testing Connectivity

### Test Web Server
```powershell
curl http://localhost:8080
```

### Test Database
```powershell
docker exec project-akhir-db mysqladmin -u root -psecret ping
```

### Test Health Endpoint
```powershell
curl http://localhost:8080/health.php | ConvertFrom-Json
```

---

## 🎯 Next Steps

1. ✅ Docker containers running
2. ✅ Web app accessible at http://localhost:8080
3. 📝 Setup database dengan `docker/mysql/init.sql`
4. 👤 Buat user account via register page
5. 🔐 Login ke aplikasi
6. 📊 Manage database via phpMyAdmin

---

## 📞 Need Help?

Cek:
1. Docker Desktop running?
2. Port 8080 available?
3. Database healthy?
4. Log files untuk error details

**Check status:**
```powershell
.\check-status.ps1
```

---

**Last Updated:** December 2025
**Ready to launch!** 🚀
