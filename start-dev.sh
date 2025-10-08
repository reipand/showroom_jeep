#!/bin/bash

# Development startup script for Showroom Web Application

echo "🚀 Starting Showroom Web Application Development Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed. Please install docker-compose first."
    exit 1
fi

echo "✅ Docker is running"

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Build and start containers
echo "🔨 Building and starting containers..."
docker-compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check if services are running
echo "🔍 Checking service status..."
docker-compose ps

# Display access information
echo ""
echo "🎉 Development environment is ready!"
echo ""
echo "📱 Access your application:"
echo "   🌐 Website: http://localhost:8080"
echo "   🗄️  phpMyAdmin: http://localhost:8081"
echo "   🐘 Database: localhost:3307"
echo ""
echo "📊 Database credentials:"
echo "   Database: jeep_db"
echo "   Username: jeep_user"
echo "   Password: bcst2526"
echo "   Root Password: bcst2526"
echo ""
echo "🛠️  Useful commands:"
echo "   View logs: docker-compose logs -f"
echo "   Stop services: docker-compose down"
echo "   Restart web: docker-compose restart web"
echo "   Restart database: docker-compose restart db"
echo ""
echo "Happy coding! 🚀"
