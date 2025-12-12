FROM dunglas/frankenphp

# Install system dependencies including Node.js
RUN apt-get update && apt-get install -y \
    default-mysql-client postgresql-client \
    procps net-tools nano git curl unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using install-php-extensions (recommended by FrankenPHP)
# If install-php-extensions is not available, fall back to docker-php-ext-install
RUN if command -v install-php-extensions >/dev/null 2>&1; then \
        install-php-extensions \
            pcntl \
            pdo_mysql \
            pdo_pgsql \
            zip \
            mbstring \
            bcmath \
            gd \
            intl \
            exif \
            opcache; \
    else \
        apt-get update && apt-get install -y \
            libexif-dev libpq-dev libzip-dev \
            libpng-dev libonig-dev libxml2-dev \
            libfreetype6-dev libjpeg-dev libicu-dev \
            && docker-php-ext-configure gd --with-freetype --with-jpeg \
            && docker-php-ext-install \
                pcntl pdo_mysql pdo_pgsql zip mbstring bcmath gd intl exif opcache \
            && apt-get clean && rm -rf /var/lib/apt/lists/*; \
    fi

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ✅ Copy only composer files first (for caching)
COPY composer.json composer.lock* ./

# Install Composer dependencies
# Note: --no-scripts is used to avoid running Laravel scripts that require .env file
# Scripts will run later in the entrypoint after .env is created
# Use --ignore-platform-reqs to avoid platform requirement issues during Docker build
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs

# ✅ Copy package files for npm (for caching)
COPY package.json package-lock.json* ./

# Install npm dependencies
RUN if [ -f "package.json" ]; then \
        npm ci --production=false || npm install; \
    fi

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

EXPOSE 8000

# FrankenPHP runs as root but switches to www-data for requests
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["octane"]
