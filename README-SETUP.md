# Quick Start Guide - Project Akhir

## Prerequisites
- Docker Desktop (Windows/Mac) atau Docker Engine (Linux)
- Git (optional)

## Step 1: Jalankan Docker Compose
```bash
docker-compose up -d
```

Atau rebuild jika ada perubahan:
```bash
docker-compose up -d --build
```

## Step 2: Akses Web App
- **Main App:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **Portainer (Container Management):** http://localhost:9000

## Step 3: Setup Database (First Time)
```bash
# Jalankan initialization script
docker exec project-akhir-db mysql -u root -psecret project_akhir < docker/mysql/init.sql
```

## Useful Commands

### View Logs
```bash
# Semua container
docker-compose logs -f

# Specific container
docker-compose logs -f webserver
docker-compose logs -f app
docker-compose logs -f db
```

### Stop Containers
```bash
docker-compose down
```

### Stop & Remove Volumes (Reset Database)
```bash
docker-compose down -v
```

### Rebuild Containers
```bash
docker-compose up -d --build
```

### Connect to Database
```bash
# Via phpMyAdmin: http://localhost:8081
# Host: db
# User: root
# Password: secret
```

### Access PHP Container Shell
```bash
docker exec -it project-akhir-app bash
```

### Access MySQL Container Shell
```bash
docker exec -it project-akhir-db bash
```

## Troubleshooting

### Port Already in Use
```bash
# Change port in docker-compose.yml
# webserver port: 8080:80 → 8089:80 (example)
```

### Database Connection Failed
1. Tunggu 60 detik (database startup)
2. Cek logs: `docker-compose logs db`
3. Verify credentials di `docker-compose.yml`

### Permission Denied Errors
```bash
# On Linux, run:
sudo chown -R $USER:$USER .
```

## Environment Variables
Lihat `.env` file untuk konfigurasi aplikasi

## Project Structure
```
├── auth/                 # Authentication files
├── config/              # Configuration files
├── dashboard/           # Dashboard pages
├── database/            # Database setup scripts
├── docker/              # Docker configuration
│   ├── nginx/          # Nginx configuration
│   ├── php/            # PHP configuration
│   └── mysql/          # MySQL configuration
├── includes/            # Shared includes (header, footer, etc)
├── logs/                # Application logs
├── monitoring/          # Monitoring pages
├── assets/              # CSS, JS, images
├── index.php            # Application entry point
├── docker-compose.yml   # Docker Compose configuration
└── Dockerfile          # PHP/Apache Docker image
```

## Network Diagram
```
┌─────────────────────┐
│   Browser           │
│   :8080, :8081     │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │              │
┌───▼────┐    ┌──▼─────┐
│ Nginx  │    │ PHP    │
│ :80    │───▶│ App    │
└────────┘    └───┬────┘
                  │
              ┌───▼─────┐
              │  MySQL  │
              │  :3306  │
              └─────────┘
```

## Default Credentials
- **Database Root:** root / secret
- **Database User:** project_user / userpass
- **Database Name:** project_akhir
- **App URL:** http://localhost:8080

---
**Last Updated:** December 2025
