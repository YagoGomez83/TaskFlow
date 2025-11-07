FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Install MySQL client for healthcheck
RUN apt-get install -y default-mysql-client

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

# Wait for database and start server
CMD bash -c "until php artisan db:show 2>/dev/null; do echo 'Waiting for database...'; sleep 2; done && php artisan serve --host=0.0.0.0 --port=8000"
