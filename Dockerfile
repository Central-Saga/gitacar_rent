# =============================================================================
#  🚗  GitaCar Rent — Production Dockerfile
#  Multi-stage build: vendor → frontend → production
# =============================================================================

# ───────────────────────────────────────────
# Stage 1: Vendor — PHP Dependencies
# ───────────────────────────────────────────
FROM php:8.3-cli-alpine AS vendor

# PHP extensions required by Laravel + spatie/laravel-medialibrary
RUN apk add --no-cache \
        postgresql-dev \
        libzip-dev \
        unzip \
        git \
        curl \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        bcmath \
        exif \
        zip \
        pcntl \
    && docker-php-ext-enable exif

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency manifests first (layer caching)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Copy the rest of the application
COPY . .

# Generate optimized autoloader + run scripts
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# ───────────────────────────────────────────
# Stage 2: Frontend — Vite Assets
# ───────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /app

# Copy package manifests
COPY package.json package-lock.json ./
RUN npm ci --no-optional 2>/dev/null || npm install

# Copy Vite config & source
COPY vite.config.js package.json ./
COPY resources/ resources/
COPY public/ public/

# Build production assets
RUN npm run build

# ───────────────────────────────────────────
# Stage 3: Production — Nginx + PHP-FPM
# ───────────────────────────────────────────
FROM php:8.3-fpm-alpine AS production

# Install Nginx, PHP extensions, and tools
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libzip-dev \
        postgresql-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        bcmath \
        exif \
        zip \
        pcntl \
    && docker-php-ext-enable exif

# Copy application from vendor stage
COPY --from=vendor /app /var/www/html

# Copy built frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Laravel storage setup
RUN mkdir -p /var/www/html/storage/framework/{cache,sessions,testing,views} \
    && mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Nginx configuration
RUN rm -f /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/laravel.conf

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
