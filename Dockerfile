FROM dunglas/frankenphp

RUN apt-get update && apt-get install -y \
    libexif-dev default-mysql-client postgresql-client libpq-dev \
    procps net-tools nano git curl unzip zip libzip-dev \
    libpng-dev libonig-dev libxml2-dev libfreetype6-dev libjpeg-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring bcmath gd intl exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ✅ Copy only composer files first (for caching)
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

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
