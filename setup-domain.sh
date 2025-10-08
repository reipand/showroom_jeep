#!/bin/bash

# Script untuk setup domain lokal showroom web
# Jalankan dengan: sudo ./setup-domain.sh

echo "🌐 Setting up domain for Showroom Web..."

# Cek apakah script dijalankan sebagai root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Please run this script as root (use sudo)"
    exit 1
fi

# Domain yang akan digunakan
DOMAIN="showroom.local"
HOSTS_FILE="/etc/hosts"

echo "📝 Adding domain to hosts file..."

# Cek apakah domain sudah ada di hosts file
if grep -q "$DOMAIN" "$HOSTS_FILE"; then
    echo "✅ Domain $DOMAIN already exists in hosts file"
else
    # Tambahkan domain ke hosts file
    echo "127.0.0.1 $DOMAIN" >> "$HOSTS_FILE"
    echo "✅ Added $DOMAIN to hosts file"
fi

echo ""
echo "🎉 Domain setup complete!"
echo ""
echo "📋 You can now access your application using:"
echo "   • http://$DOMAIN:8080"
echo "   • http://localhost:8080 (still works)"
echo ""
echo "🔧 To use a custom domain:"
echo "   1. Edit /etc/hosts file"
echo "   2. Add: 127.0.0.1 yourdomain.com"
echo "   3. Update nginx.conf server_name"
echo "   4. Restart containers: docker compose restart"
echo ""
echo "🌍 For production domain:"
echo "   1. Point your domain DNS to your server IP"
echo "   2. Update nginx.conf server_name"
echo "   3. Setup SSL certificates"
echo "   4. Uncomment HTTPS configuration in nginx.conf"
