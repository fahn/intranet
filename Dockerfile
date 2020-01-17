#
# docker container for github intranet
#
FROM php:7.2-apache

LABEL maintainer="Stefan Metzner <stefan@weinekind.de>"
LABEL version="1.0.6.4"

ARG COMPOSER_MD5="baf1608c33254d00611ac1705c1d9958c817a1a33bce370c0595974b342601bd80b92a3f46067da89e3b06bff421f182"

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

RUN test -f index.html && rm index.html || true

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

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === ${COMPOSER_MD5}) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

USER www-data
WORKDIR /var/www/html/

# install composer requiredments
## parse md-files
RUN composer install

# Volumes
VOLUME /var/www/html/inc/config.ini
VOLUME /var/www/html/static/img/background.jpg
VOLUME /var/www/html/static/img/background_mobil.jpg

# Ports
EXPOSE 80

ENTRYPOINT [ "/usr/sbin/apache2ctl" ]

CMD [ "-D", "FOREGROUND" ]
