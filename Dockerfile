# Verwende ein offizielles PHP-Apache-Image als Basis
FROM php:7.4-apache

# Installiere Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installiere zusätzliche PHP-Erweiterungen, falls benötigt
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    msmtp \
    g++ \
    git \
    wget \
    zip \
    zlib1g-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql iconv gd zip intl pdo_mysql mbstring zip intl pdo_mysql mbstring

# Kopiere den Quellcode ins Apache-Verzeichnis
COPY src/ /var/www/html/


# Kopiere die Konfigurationen für PHP
COPY docker-build/intranet/php/* /usr/local/etc/php/conf.d/

# Optional: Aktiviere Apache-Modul-Rewrites
RUN a2enmod http2 expires deflate rewrite session

# Setze korrekte Berechtigungen (optional, je nach Anwendungsfall)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html



# Installiere PHP-Abhängigkeiten mit Composer, falls eine composer.json existiert
COPY composer.json /var/www/html/composer.json

WORKDIR /var/www/html


# HEALTHCHECK
HEALTHCHECK --interval=60s --timeout=30s CMD nc -zv localhost 80 || exit 1

# Composer install
RUN if [ -f composer.json ]; then composer install --no-scripts; fi

# Ports
EXPOSE 80