    FROM php:8.2-apache

    # Install system dependencies
    RUN apt-get update && apt-get install -y \
        git curl zip unzip libpng-dev libonig-dev libxml2-dev

    # Install PHP extensions
    RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

    # Enable Apache rewrite
    RUN a2enmod rewrite

    # Set working directory
    WORKDIR /var/www

    # Copy project
    COPY . .

    # Install Composer
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

    # Install Laravel dependencies
    RUN composer install --no-dev --optimize-autoloader

    # Fix permissions
    RUN chown -R www-data:www-data storage bootstrap/cache

    # Expose port
    EXPOSE 80

    RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf