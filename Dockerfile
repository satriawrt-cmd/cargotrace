FROM php:8.2-cli

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libsqlite3-dev \
    sqlite3 && \
    docker-php-ext-install pdo_sqlite mbstring gd bcmath && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy repository code
COPY . .

# Install PHP packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Prepare environment file and permissions
RUN cp .env.example .env && \
    touch database/database.sqlite && \
    chmod -R 777 storage bootstrap/cache database

EXPOSE 10000

# Start Laravel
CMD sh -c "php artisan key:generate --force && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=\${PORT:-10000}"
