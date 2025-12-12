FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libexif-dev default-mysql-client postgresql-client libpq-dev \
    supervisor procps net-tools nano git curl unzip zip libzip-dev \
    libpng-dev libonig-dev libxml2-dev libfreetype6-dev libjpeg-dev libicu-dev \
    nginx \
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

RUN sed -i 's/^listen = .*/listen = 127.0.0.1:9000/' /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx configuration files
# Only copy HTTP config - SSL is handled by Coolify's Caddy reverse proxy
COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
# Remove default nginx config and any SSL configs (Coolify handles SSL)
RUN rm -f /etc/nginx/conf.d/default.conf.bak /etc/nginx/conf.d/default-ssl.conf 2>/dev/null || true

# Create Laravel cache directories with proper structure
RUN mkdir -p /var/www/storage/framework/{sessions,views,cache/data} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy supervisor config to run both PHP-FPM and Nginx
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

# PHP-FPM and Nginx need to run as root
# They will switch to www-data for individual requests
# Don't use USER www-data here - services handle user switching internally
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
