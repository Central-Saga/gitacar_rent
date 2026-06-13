# =============================================================================
#  🚗  GitaCar Rent — Production Dockerfile
#  Multi-stage build: vendor → frontend → production
# =============================================================================

# ───────────────────────────────────────────
# Stage 1: Vendor — PHP Dependencies
# ───────────────────────────────────────────
FROM php:8.3-cli-alpine AS vendor

RUN apk add --no-cache \
        libzip-dev \
        unzip \
        git \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        exif \
        gd \
        zip \
        pcntl \
    && docker-php-ext-enable exif

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

COPY . .
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

COPY --from=vendor /app/vendor /app/vendor
COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js package.json ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# ───────────────────────────────────────────
# Stage 3: Production — Nginx + PHP-FPM
# ───────────────────────────────────────────
FROM php:8.3-fpm-alpine AS production

RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        exif \
        gd \
        zip \
        pcntl \
    && docker-php-ext-enable exif

COPY --from=vendor /app /var/www/html

COPY --from=frontend /app/public/build /var/www/html/public/build

RUN mkdir -p /var/www/html/storage/framework/{cache,sessions,testing,views} \
    && mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Publish Livewire and Flux assets to public directory
RUN mkdir -p /var/www/html/public/vendor/livewire /var/www/html/public/flux \
    && cp -r /var/www/html/vendor/livewire/livewire/dist/. /var/www/html/public/vendor/livewire/ \
    && cp -r /var/www/html/vendor/livewire/flux/dist/. /var/www/html/public/flux/

RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf

RUN rm -f /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/laravel.conf

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
