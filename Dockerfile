FROM dunglas/frankenphp:1-php8.5-bookworm

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install system dependencies required by Composer and SQLite
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions for SQLite
RUN install-php-extensions pdo_sqlite

WORKDIR /app

# Install dependencies first (leverages Docker cache)
COPY composer.json composer.lock ./
# PHP 8.5 is new — many packages lack explicit 8.5 platform declarations yet
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Copy source code
COPY . .

# Ensure runtime directories exist
RUN mkdir -p var/cache var/log var/data

# Worker mode configuration
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

EXPOSE 80

# Entrypoint handles dynamic PORT binding (Railway) and SQLite init
ENTRYPOINT ["./docker-entrypoint.sh"]
