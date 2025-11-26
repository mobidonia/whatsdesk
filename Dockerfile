FROM php:8.2-fpm

# Install system dependencies and PHP extensions, you can add nodejs and npm
RUN apt-get update && apt-get install -y \
    nginx \
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
    && docker-php-ext-install exif \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_pgsql zip mbstring bcmath gd intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Remove default nginx page and configs to avoid conflicts
RUN rm -f /var/www/html/index.nginx-debian.html \
    && rm -f /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-available/default

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure nginx
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisord config
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . .

RUN chmod +x /var/www/post_deploy/post_deploy_wpbox_free.sh
RUN chmod +x /var/www/post_deploy/post_deploy_loyalty.sh
RUN chmod +x /var/www/post_deploy/post_deploy_whatsdesk.sh

# Fix Laravel folder permissions
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 775 public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache /var/www

# Declare persistent paths
VOLUME ["/var/www/storage/app","/var/www/public/uploads"]

# Expose HTTP port
EXPOSE 80 443

# Start supervisord
CMD ["/usr/bin/supervisord", "-n"]