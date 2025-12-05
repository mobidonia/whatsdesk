FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libexif-dev \
    default-mysql-client \
    postgresql-client \
    libpq-dev \
    supervisor \
    procps \
    net-tools \
    nano \
    git \
    curl \
    unzip \
    libzip-dev \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_pgsql zip mbstring bcmath gd intl exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user || true
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Copy custom entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy application code (optional - uncomment if building image with code baked in)
# For Coolify pre-built images, code should be mounted via volumes
# If volume mounting doesn't work, uncomment these lines and rebuild the image:
# COPY --chown=www-data:www-data . /var/www
# RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction || true; fi

# Set proper permissions for Laravel directories (run as root, then switch)
RUN mkdir -p /var/www/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage \
    && chown -R www-data:www-data /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# PHP-FPM needs to run as root to bind to port 9000, but will switch to www-data for requests
# For artisan commands, we'll switch to the user in the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

