FROM dunglas/frankenphp:1-php8.5-bookworm

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install PHP extensions for MySQL
RUN install-php-extensions pdo_mysql

WORKDIR /app

# Install dependencies first (leverages Docker cache)
COPY composer.json composer.lock ./
# PHP 8.5 is new — many packages lack explicit 8.5 platform declarations yet
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Copy source code
COPY . .

# Ensure runtime directories exist
RUN mkdir -p var/cache var/log

# Plain HTTP for local development (override SERVER_NAME for production)
ENV SERVER_NAME=":80"
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

EXPOSE 80
