FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    gettext-base

# Install Node.js (needed to build frontend assets with Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . .

# Ensure storage and bootstrap/cache directories exist with write permissions
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer config --global process-timeout 2000
RUN composer install --no-dev --optimize-autoloader || \
    (echo "Composer install failed, retrying once..." && sleep 5 && composer install --no-dev --optimize-autoloader)

# Build frontend assets so Vite generates public/build/manifest.json
RUN npm install && npm run build

# Remove nginx's default site so only our own templated config (below)
# binds to Render's port — leaving both would conflict.
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

RUN mkdir -p /etc/nginx/templates
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf

RUN chmod +x docker-entrypoint.sh
ENTRYPOINT ["./docker-entrypoint.sh"]

# Render assigns the port dynamically at runtime via $PORT — nginx binds
# to it (via the templated config above), not PHP directly anymore.
EXPOSE 8000