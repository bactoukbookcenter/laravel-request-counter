FROM serversideup/php:8.5-frankenphp

# Install Node.js for asset building
ARG NODE_VERSION=22

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files and build frontend assets
COPY package.json package-lock.json* ./
RUN npm ci

# Copy the rest of the application
COPY . .

# Complete composer install (autoloader, scripts)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Build frontend assets
RUN npm run build && rm -rf node_modules

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
