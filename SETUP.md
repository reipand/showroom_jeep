# 🚀 Showroom Web - Setup Guide

## ✅ Setup Complete!

Your Docker-based development environment is now fully configured and working. Here's what has been set up:

### 🐳 Docker Services

1. **Web Server** (PHP 8.2 + Apache)
   - Port: `8080`
   - URL: http://localhost:8080
   - Status: ✅ Running

2. **Database** (MariaDB 10.7)
   - Port: `3307`
   - Database: `jeep_db`
   - User: `jeep_user`
   - Password: `bcst2526`
   - Status: ✅ Running

3. **phpMyAdmin**
   - Port: `8081`
   - URL: http://localhost:8081
   - Status: ✅ Running

### 🔧 Configuration Features

- **Environment Detection**: Automatically detects Docker vs local development
- **Database Auto-Initialization**: Database schema is automatically imported
- **Health Checks**: Built-in health monitoring for containers
- **CI/CD Ready**: GitHub Actions workflow configured for deployment

### 🚀 Quick Start Commands

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f

# Stop services
docker compose down

# Restart web service
docker compose restart web

# Access web container
docker compose exec web bash

# Access database
docker compose exec db mysql -u jeep_user -pbcst2526 jeep_db
```

### 📱 Access Points

- **Main Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Database**: localhost:3307

### 🔄 CI/CD Pipeline

The GitHub Actions workflow is configured to:
1. Build Docker image on push to `reisan_backend` branch
2. Push image to Docker Hub
3. Deploy to production server via SSH

### 🛠️ Development Workflow

1. **Local Development**: Use `docker compose up -d` for full environment
2. **Code Changes**: Edit files in `src/` directory (auto-synced)
3. **Database Changes**: Modify `jeep_db.sql` and restart containers
4. **Deployment**: Push to `reisan_backend` branch triggers CI/CD

### 📁 Project Structure

```
showroom_web/
├── src/                    # Application source code
├── .Dockerfile            # Docker image configuration
├── docker-compose.yml     # Docker services
├── jeep_db.sql           # Database schema
├── .github/workflows/    # CI/CD configuration
├── start-dev.sh          # Development startup script
└── README.md             # Full documentation
```

### 🎯 Next Steps

1. **Test the application**: Visit http://localhost:8080
2. **Access phpMyAdmin**: Visit http://localhost:8081
3. **Configure GitHub Secrets**: Set up CI/CD deployment
4. **Start developing**: Edit files in `src/` directory

### 🆘 Troubleshooting

If you encounter issues:

```bash
# Check container status
docker compose ps

# View logs
docker compose logs

# Restart all services
docker compose down && docker compose up -d

# Rebuild containers
docker compose build --no-cache
```

---

**🎉 Your development environment is ready! Happy coding!**
