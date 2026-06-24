# =============================================================================
#  🚗  GitaCar Rent — Production Dockerfile
# Multi-stage build: vendor → production (frontend assets built in CI)
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
# Stage 2: Production — Nginx + PHP-FPM
# ───────────────────────────────────────────
# NOTE: Vite assets (public/build/) are built in CI and included in the
# Docker context.  The frontend build stage was removed to guarantee that
# the CSS/JS hashes match between the CI manifest check and the served
# container — Tailwind v4 + Vite produce non-deterministic hashes across
# different OS / build environments.

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

COPY public/build /var/www/html/public/build

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

RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

RUN rm -f /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/laravel.conf

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
