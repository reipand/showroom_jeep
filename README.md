# Showroom Web Application

Aplikasi web showroom untuk penjualan kendaraan Jeep dengan sistem manajemen pengguna, katalog kendaraan, dan pemesanan.

## 🚀 Fitur

- **Sistem Autentikasi**: Login/Register dengan role-based access (Pelanggan, Sales, Admin)
- **Katalog Kendaraan**: Manajemen katalog kendaraan Jeep
- **Sistem Pemesanan**: Proses pemesanan kendaraan
- **Dashboard Admin**: Panel administrasi untuk mengelola data
- **360° Viewer**: Fitur preview kendaraan 360 derajat

## 🛠️ Teknologi

- **Backend**: PHP 8.2 dengan Apache
- **Database**: MariaDB 10.7
- **Frontend**: HTML, CSS, JavaScript
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions

## 📋 Prerequisites

- Docker & Docker Compose
- Git
- Web browser

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone <repository-url>
cd showroom_web
```

### 2. Development dengan Docker

#### Jalankan aplikasi lengkap (Web + Database + phpMyAdmin):
```bash
docker-compose up -d
```

#### Akses aplikasi:
- **Website**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Database**: localhost:3307

#### Stop aplikasi:
```bash
docker-compose down
```

### 3. Development Lokal (tanpa Docker)

#### Setup Database:
1. Install XAMPP/WAMP/LAMP
2. Import `jeep_db.sql` ke database MySQL/MariaDB
3. Update konfigurasi di `src/config.php`

#### Jalankan aplikasi:
```bash
# Masuk ke direktori src
cd src

# Jalankan dengan PHP built-in server
php -S localhost:8000
```

## 🔧 Konfigurasi

### Environment Variables

Aplikasi mendukung konfigurasi environment untuk development dan production:

#### Docker Environment:
```bash
DB_HOST=db
DB_USER=jeep_user
DB_PASS=bcst2526
DB_NAME=jeep_db
APP_ENV=docker
```

#### Local Development:
```bash
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=jeep_db
APP_ENV=development
```

#### Production:
```bash
DB_HOST=db
DB_USER=jeep_user
DB_PASS=bcst2526
DB_NAME=jeep_db
APP_ENV=production
```

### Database Credentials

**Default credentials:**
- Database: `jeep_db`
- Username: `jeep_user`
- Password: `bcst2526`
- Root Password: `bcst2526`

## 🏗️ Struktur Project

```
showroom_web/
├── src/                    # Source code aplikasi
│   ├── assets/            # CSS, JS, Images
│   ├── includes/          # Header, Footer
│   ├── config.php         # Konfigurasi aplikasi
│   ├── koneksi.php        # Database connection
│   ├── login.php          # Halaman login
│   ├── register.php       # Halaman register
│   ├── dashboard.php      # Dashboard admin
│   └── ...
├── .Dockerfile            # Docker image configuration
├── docker-compose.yml     # Docker services configuration
├── jeep_db.sql           # Database schema
├── init-db.sh            # Database initialization script
└── .github/workflows/    # CI/CD configuration
```

## 🔄 CI/CD Pipeline

### GitHub Actions Workflow

Pipeline otomatis yang berjalan saat push ke branch `reisan_backend`:

1. **Build**: Build Docker image
2. **Push**: Push image ke Docker Hub
3. **Deploy**: Deploy ke server production via SSH

### Setup CI/CD

#### 1. GitHub Secrets
Tambahkan secrets berikut di GitHub repository:

```
DOCKER_USERNAME=your-dockerhub-username
DOCKER_TOKEN=your-dockerhub-token
SSH_HOST=your-server-ip
SSH_USER=your-server-username
SSH_KEY=your-private-ssh-key
```

#### 2. Docker Hub
- Buat repository di Docker Hub
- Update `DOCKER_IMAGE_NAME` di `.github/workflows/deploy.yml`

## 🐳 Docker Commands

### Development
```bash
# Build dan jalankan semua services
docker-compose up -d

# Lihat logs
docker-compose logs -f

# Restart service tertentu
docker-compose restart web

# Stop semua services
docker-compose down

# Stop dan hapus volumes
docker-compose down -v
```

### Production
```bash
# Pull image terbaru
docker pull reipand/showroom_jeep:latest

# Jalankan container production
docker run -d \
  -p 8080:80 \
  --name php-webserver \
  --restart unless-stopped \
  -e APP_ENV=production \
  -e DB_HOST=db \
  -e DB_USER=jeep_user \
  -e DB_PASS=bcst2526 \
  -e DB_NAME=jeep_db \
  reipand/showroom_jeep:latest
```

## 🔍 Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check database container status
docker-compose ps

# Check database logs
docker-compose logs db

# Restart database
docker-compose restart db
```

#### 2. Permission Issues
```bash
# Fix file permissions
sudo chown -R $USER:$USER src/
chmod -R 755 src/
```

#### 3. Port Already in Use
```bash
# Check port usage
sudo netstat -tulpn | grep :8080

# Kill process using port
sudo kill -9 <PID>
```

### Debug Mode

Enable debug mode untuk development:
```php
// Di src/config.php
$config['app']['debug'] = true;
```

## 📝 API Documentation

### Database Schema

#### Users Table
- `id`: Primary key
- `full_name`: Nama lengkap user
- `email`: Email (unique)
- `phone_number`: Nomor telepon
- `password_hash`: Hash password
- `role`: Role user (pelanggan, sales, admin)
- `created_at`: Timestamp

#### Vehicles Table
- `id`: Primary key
- `name`: Nama kendaraan
- `model_year`: Tahun model
- `price`: Harga
- `stock`: Stok tersedia
- `description`: Deskripsi
- `image_file`: File gambar
- `is_active`: Status aktif
- `created_at`: Timestamp

#### Orders Table
- `id`: Primary key
- `user_id`: ID user yang memesan
- `vehicle_id`: ID kendaraan
- `order_date`: Tanggal pemesanan
- `quantity`: Jumlah
- `total_amount`: Total harga
- `order_status`: Status pesanan
- `sales_id`: ID sales yang menangani

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

Untuk pertanyaan atau bantuan, silakan buat issue di repository ini.

---

**Happy Coding! 🚀**