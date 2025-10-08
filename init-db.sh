#!/bin/bash

# Database initialization script for Docker
# This script will be executed when the MariaDB container starts for the first time

echo "Starting database initialization..."

# Wait for MariaDB to be ready
until mysqladmin ping -h"$HOST" -P"$PORT" -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --silent; do
    echo "Waiting for MariaDB to be ready..."
    sleep 2
done

echo "MariaDB is ready. Initializing database..."

# Create database if it doesn't exist
mysql -h"$HOST" -P"$PORT" -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;"

# Import the database schema
if [ -f "/docker-entrypoint-initdb.d/jeep_db.sql" ]; then
    echo "Importing database schema..."
    mysql -h"$HOST" -P"$PORT" -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/jeep_db.sql
    echo "Database schema imported successfully."
else
    echo "Warning: jeep_db.sql not found in /docker-entrypoint-initdb.d/"
fi

echo "Database initialization completed."
