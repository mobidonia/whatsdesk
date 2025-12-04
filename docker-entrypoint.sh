#!/bin/sh

# Exit on fail
set -e


# Get database connection details
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_USERNAME=${DB_USERNAME:-laravel}
DB_PASSWORD=${DB_PASSWORD:-root}
DB_DATABASE=${DB_DATABASE:-laravel}

# Wait for MySQL to be ready (only if DB_HOST is set and not empty)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Waiting for database to be ready..."
    max_attempts=30
    attempt=0
    
    until php -r "
    try {
        \$host = getenv('DB_HOST') ?: 'db';
        \$port = getenv('DB_PORT') ?: '3306';
        \$user = getenv('DB_USERNAME') ?: 'laravel';
        \$pass = getenv('DB_PASSWORD') ?: 'root';
        \$pdo = new PDO('mysql:host=' . \$host . ';port=' . \$port, \$user, \$pass);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
    " 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ $attempt -ge $max_attempts ]; then
            echo "Database connection failed after $max_attempts attempts. Continuing anyway..."
            break
        fi
        echo "Database is unavailable - sleeping (attempt $attempt/$max_attempts)"
        sleep 2
    done
    echo "Database is ready!"
fi

# Fix permissions for Laravel directories
echo "Setting up permissions..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Install dependencies if vendor missing (useful for dev/first run)
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-progress --no-interaction --optimize-autoloader
fi

# Set up env file if missing
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        echo "Warning: .env.example not found. Please create .env manually."
    fi
fi

# Generate key if missing or empty
if [ -f ".env" ]; then
    if ! grep -q "APP_KEY=base64:" .env || grep -q "APP_KEY=$" .env; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi
    
    # Ensure Reverb environment variables are set (for reverb service)
    if ! grep -q "REVERB_APP_ID=" .env; then
        echo "Setting Reverb environment variables..."
        echo "" >> .env
        echo "# Reverb Configuration" >> .env
        echo "REVERB_APP_ID=${REVERB_APP_ID:-whatsdesk}" >> .env
        echo "REVERB_APP_KEY=${REVERB_APP_KEY:-whatsdesk-key}" >> .env
        echo "REVERB_APP_SECRET=${REVERB_APP_SECRET:-whatsdesk-secret}" >> .env
        echo "REVERB_HOST=${REVERB_HOST:-0.0.0.0}" >> .env
        echo "REVERB_PORT=${REVERB_PORT:-8080}" >> .env
        echo "REVERB_SCHEME=${REVERB_SCHEME:-http}" >> .env
    fi
fi

# Run migrations only if we're the main app container (not reverb/queue)
if [ "$1" = "php-fpm" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "Migration failed or already up to date"
    
    # Cache configuration for better performance
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Execute the main command passed to the container (e.g., php-fpm or reverb start)
exec "$@"