FROM --platform=linux/amd64 serversideup/php:8.5-frankenphp

ARG NODE_VERSION=22

USER root

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY docker/opentelemetry-1.2.1.tgz /tmp/

RUN install-php-extensions intl \
    && pecl install /tmp/opentelemetry-1.2.1.tgz \
    && docker-php-ext-enable opentelemetry \
    && rm /tmp/opentelemetry-1.2.1.tgz

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files and build frontend assets
COPY package.json package-lock.json ./
RUN npm ci

# Copy the rest of the application
COPY . .

# Complete composer install (autoloader, scripts)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Build frontend assets
RUN npm run build && rm -rf node_modules

# Ensure Laravel's required runtime directories exist (excluded from .dockerignore)
RUN mkdir -p storage/framework/cache/data \
              storage/framework/sessions \
              storage/framework/testing \
              storage/framework/views \
              storage/logs \
              bootstrap/cache

# Set permissions for Laravel storage and bootstrap cache, then drop to www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

USER www-data
