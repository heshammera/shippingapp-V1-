#!/bin/bash
set -e

# Set APP_URL if not set, using Render's env var
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi

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
