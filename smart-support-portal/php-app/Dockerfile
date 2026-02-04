# php-app/Dockerfile
FROM php:8.2-fpm

# Install build deps and extensions (Postgres + Redis)
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  libpq-dev \
  libzip-dev \
  unzip \
  git \
  ca-certificates \
  gcc \
  make \
  && docker-php-ext-install pdo_pgsql \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app source
COPY . /var/www/html

# Ensure file ownership for FPM user and permissions
RUN chown -R www-data:www-data /var/www/html \
  && find /var/www/html -type d -exec chmod 755 {} + \
  && find /var/www/html -type f -exec chmod 644 {} +

# Expose php-fpm port (for upstream like nginx)
EXPOSE 9000

CMD ["php-fpm"]
