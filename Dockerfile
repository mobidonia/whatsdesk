FROM dunglas/frankenphp

# Install system dependencies
RUN apt-get update && apt-get install -y \
    default-mysql-client postgresql-client \
    procps net-tools nano git curl unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using install-php-extensions (recommended by FrankenPHP)
RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    zip \
    mbstring \
    bcmath \
    gd \
    intl \
    exif \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ✅ Copy only composer files first (for caching)
COPY composer.json composer.lock* ./

# Install Composer dependencies
# Note: --no-scripts is used to avoid running Laravel scripts that require .env file
# Scripts will run later in the entrypoint after .env is created
# Increase memory limit for Composer and set platform requirements
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# ✅ Now copy full project
COPY . .

# Create Laravel cache directories with proper structure
RUN mkdir -p /var/www/storage/framework/{sessions,views,cache/data} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ⛔ IMPORTANT: Override FrankenPHP default port 9000
ENV FRANKENPHP_CONFIG="worker /var/www/public { listen 0.0.0.0:80 }"

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# FrankenPHP runs as root but switches to www-data for requests
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["octane"]
