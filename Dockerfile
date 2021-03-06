#
# docker container for github rangliste
#
FROM php:7.2-apache

LABEL maintainer="Stefan Metzner <stefan@weinekind.de>"
LABEL version="1.0.6.4"

ARG COMPOSER_MD5="a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1"

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
    && docker-php-ext-install zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# set msmtp as sendmail
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/docker-php-sendmail.ini && \
    echo -e "max_execution_time 120\npost_max_size 20M\nupload_max_filesize 20M\nupload_tmp_dir /tmp\n" > /usr/local/etc/php/conf.d/docker-php-intranet.ini

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
    /var/www/html/static/img/background_mobil.jpg

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

USER root

# Volumes
VOLUME /var/www/html/inc/config.ini
VOLUME /var/www/html/static/img/background.jpg
VOLUME /var/www/html/static/img/background_mobil.jpg

# Ports
EXPOSE 80

ENTRYPOINT [ "/usr/sbin/apache2ctl" ]

CMD [ "-D", "FOREGROUND" ]
