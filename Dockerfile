#
# docker container for github intranet
#
FROM php:7.3-apache

LABEL maintainer="Stefan Metzner <stefan@weinekind.de>"
LABEL version="1.0.6.4"


# install os requirements
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
        libicu-dev \
        msmtp \
        g++ \
        git \
        wget \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# install php requirements
RUN docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip intl pdo_mysql mbstring\
    && docker-php-ext-configure intl \
    && docker-php-ext-enable pdo_mysql

# create log for msmtp
RUN touch /var/log/msmtp.log

# copy custom php ini
COPY build/php/* /usr/local/etc/php/conf.d/

# enable modules for apache
RUN a2enmod http2 expires deflate rewrite session

WORKDIR /var/www/html/

RUN rm -rf index.html

# COPY from html
COPY html/ /var/www/html/

# REMOVE
RUN rm -rf .git* \
    templates_c/* \
    Tests \
    /var/www/html/inc/config.ini \
    /var/www/html/static/img/background.jpg \
    /var/www/html/static/img/background_mobil.jpg \
    /var/www/html/static/img/user \
    /var/www/html/static/img/favicon

# set rights
RUN chmod -R 755 . && chown -R www-data:www-data .

RUN COMPOSER_MD5="$(wget -q -O - https://composer.github.io/installer.sig)" && \
    echo $COMPOSER_MD5

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '${COMPOSER_MD5}') { unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer  && \
    php -r "unlink('composer-setup.php');"

USER www-data
WORKDIR /var/www/html/

# install composer requiredments
## parse md-files
RUN composer install

# Volumes
VOLUME [ "/var/www/html/inc/config.ini" ]
VOLUME [ "/var/www/html/static/img/background.jpg" ]
VOLUME [ "/var/www/html/static/img/background_mobil.jpg" ]
VOLUME [ "/var/www/html/static/img/favicon" ]
VOLUME [ "/etc/aliases" ]
VOLUME [ "/etc/msmtprc" ]

# Ports
EXPOSE 80
