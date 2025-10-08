# 🌐 Domain Setup Guide untuk Showroom Web

## 📋 Opsi Domain yang Tersedia

### 1. **Domain Lokal (Development)**
- **URL**: `http://showroom.local:8080`
- **Cara**: Tambahkan ke `/etc/hosts`

### 2. **Domain Production**
- **URL**: `https://yourdomain.com`
- **Cara**: Point DNS ke server IP

### 3. **Subdomain**
- **URL**: `https://showroom.yourdomain.com`
- **Cara**: Setup subdomain di DNS

---

## 🚀 Setup Domain Lokal (Development)

### Step 1: Tambahkan Domain ke Hosts File
```bash
# Edit hosts file
sudo nano /etc/hosts

# Tambahkan baris ini:
127.0.0.1 showroom.local
```

### Step 2: Restart Container
```bash
cd /home/reip/Documents/showroom_web
docker compose restart
```

### Step 3: Test Domain
```bash
# Test dengan curl
curl http://showroom.local:8080

# Atau buka di browser
# http://showroom.local:8080
```

---

## 🌍 Setup Domain Production

### Step 1: Point DNS ke Server
1. Login ke domain registrar (GoDaddy, Namecheap, dll)
2. Update A record: `yourdomain.com` → `YOUR_SERVER_IP`
3. Update A record: `www.yourdomain.com` → `YOUR_SERVER_IP`

### Step 2: Update Nginx Configuration
```bash
# Copy production config
cp nginx-production.conf nginx.conf

# Edit nginx.conf
nano nginx.conf

# Update server_name:
server_name yourdomain.com www.yourdomain.com;
```

### Step 3: Setup SSL Certificate
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Step 4: Deploy
```bash
# Restart containers
docker compose restart

# Test
curl https://yourdomain.com
```

---

## 🔧 Konfigurasi Docker Compose untuk Domain

### Update docker-compose.yml
```yaml
services:
  web:
    # ... existing config ...
    ports:
      - "80:80"    # HTTP
      - "443:443"  # HTTPS (for production)
    volumes:
      - /home/reip/Documents/showroom_web/src:/var/www/html
      - /home/reip/Documents/showroom_web/nginx.conf:/etc/nginx/conf.d/default.conf
      # For SSL certificates (production)
      - /etc/letsencrypt:/etc/letsencrypt:ro
```

---

## 📱 URL yang Bisa Digunakan

### Development
- ✅ `http://localhost:8080`
- ✅ `http://showroom.local:8080` (setelah setup hosts)
- ✅ `http://127.0.0.1:8080`

### Production
- ✅ `https://yourdomain.com`
- ✅ `https://www.yourdomain.com`
- ✅ `https://showroom.yourdomain.com` (subdomain)

---

## 🛠️ Troubleshooting

### Domain tidak bisa diakses
```bash
# Cek hosts file
cat /etc/hosts | grep showroom

# Cek DNS
nslookup yourdomain.com

# Cek container status
docker compose ps
```

### SSL Certificate Issues
```bash
# Renew certificate
sudo certbot renew

# Check certificate status
sudo certbot certificates
```

### Nginx Configuration Issues
```bash
# Test nginx config
docker compose exec web nginx -t

# Reload nginx
docker compose exec web nginx -s reload
```

---

## 📝 Quick Commands

```bash
# Setup domain lokal
echo "127.0.0.1 showroom.local" | sudo tee -a /etc/hosts

# Restart containers
docker compose restart

# Test domain
curl http://showroom.local:8080

# Check logs
docker compose logs web
```

---

## 🎯 Next Steps

1. **Development**: Setup domain lokal dengan hosts file
2. **Production**: Point DNS ke server dan setup SSL
3. **Monitoring**: Setup monitoring dan backup
4. **Performance**: Optimize nginx dan database

---

## 📞 Support

Jika ada masalah dengan domain setup, cek:
1. DNS propagation: https://dnschecker.org/
2. SSL certificate: https://www.ssllabs.com/ssltest/
3. Container logs: `docker compose logs`
