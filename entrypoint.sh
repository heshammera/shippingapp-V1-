#!/bin/bash
set -e

# Run migrations (force for automation)
echo "Running migrations..."
php artisan migrate --force

# Optimize caches
echo "Caching configuration..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
