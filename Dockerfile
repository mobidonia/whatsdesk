FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libexif-dev default-mysql-client postgresql-client libpq-dev \
    supervisor procps net-tools nano git curl unzip zip libzip-dev \
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

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

USER www-data

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
