#!/bin/bash

# Script to install Google Cloud reCAPTCHA Enterprise dependencies
echo "🔧 Installing Google Cloud reCAPTCHA Enterprise dependencies..."

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Check if installation was successful
if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully!"
    echo ""
    echo "📋 Next steps:"
    echo "1. Set up Google Cloud authentication:"
    echo "   export GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account-key.json"
    echo ""
    echo "2. Or use Application Default Credentials:"
    echo "   gcloud auth application-default login"
    echo ""
    echo "3. Restart your Docker containers:"
    echo "   docker compose restart"
    echo ""
    echo "4. Test reCAPTCHA Enterprise functionality"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi
