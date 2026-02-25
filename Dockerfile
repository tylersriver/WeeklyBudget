FROM dunglas/frankenphp:latest-php8.5-bookworm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PHP extensions for MySQL
RUN install-php-extensions pdo_mysql

WORKDIR /app

# Install dependencies first (leverages Docker cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy source code
COPY . .

# Ensure runtime directories exist
RUN mkdir -p var/cache var/log

EXPOSE 80

ENV FRANKENPHP_CONFIG="worker ./public/index.php"
