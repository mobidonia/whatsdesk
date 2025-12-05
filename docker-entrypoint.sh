#!/bin/sh

# Don't exit on error - we want PHP-FPM to start even if some setup steps fail
# set -e  # Commented out to allow PHP-FPM to start even if migrations fail

# Ensure we're in the correct working directory
cd /var/www || {
    echo "ERROR: Cannot change to /var/www directory"
    exit 1
}

# Check if application code exists
if [ ! -f "composer.json" ] && [ ! -f "artisan" ]; then
    echo "WARNING: Application code not found in /var/www"
    echo "This usually means the volume mount failed in Coolify."
    echo "Checking directory contents:"
    ls -la /var/www/ | head -20
    echo ""
    echo "If you see this error, either:"
    echo "1. Check Coolify volume mounting settings"
    echo "2. Rebuild the Docker image WITH code included (modify Dockerfile to COPY . /var/www)"
    echo ""
    # Don't exit - let PHP-FPM start anyway so healthcheck can pass
fi

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

# Install dependencies if vendor missing (useful for dev/first run or if code is mounted)
# Note: If code is baked into the image, dependencies should already be installed during build
if [ ! -d "vendor" ] && [ -f "composer.json" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-progress --no-interaction --optimize-autoloader || echo "Composer install failed, continuing..."
elif [ -d "vendor" ] && [ -f "composer.json" ]; then
    echo "Composer dependencies already installed (from image build)."
elif [ ! -f "composer.json" ]; then
    echo "Warning: composer.json not found. Code may not be mounted or copied correctly."
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
        php artisan key:generate --force || echo "Key generation failed, continuing..."
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

    # Seed the database
    php artisan db:seed --force || echo "Seeding failed or already up to date"
    
    # app:migrrate-modules
    php artisan app:migrrate-modules || echo "Migration of modules failed or already up to date"
    
    # Cache configuration for better performance (don't fail if caching fails)
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache || echo "Route cache failed, continuing..."
    php artisan view:cache || echo "View cache failed, continuing..."
    
    echo "App initialization complete!"
fi

# Always execute the main command (PHP-FPM or reverb start)
# This ensures the service starts even if migrations failed
echo "Starting service: $@"
exec "$@"