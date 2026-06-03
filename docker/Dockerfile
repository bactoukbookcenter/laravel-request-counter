FROM --platform=$BUILDPLATFORM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources/ ./resources/
COPY vite.config.js public/ ./
RUN npm run build

FROM --platform=linux/amd64 serversideup/php:8.5-frankenphp

ARG NODE_VERSION=22

USER root

COPY docker/opentelemetry-1.2.1.tgz /tmp/

RUN install-php-extensions intl \
    && pecl install /tmp/opentelemetry-1.2.1.tgz \
    && docker-php-ext-enable opentelemetry \
    && rm /tmp/opentelemetry-1.2.1.tgz

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Copy pre-built frontend assets from native build stage
COPY --from=assets /app/public/build ./public/build

# Complete composer install (autoloader, scripts)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Generate the FrankenPHP Octane worker file
RUN php artisan octane:install --server=frankenphp --no-interaction

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

CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8080"]
