#
# docker container for github rangliste
#
FROM ubuntu:latest

MAINTAINER Stefan Metzner "stefan@weinekind.de"

# upgrade system
RUN DEBIAN_FRONTEND=noninteractive apt-get update && apt-get upgrade -y

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apache2 \
    apache2-bin \
    libapache2-mod-php7.2 \
    php7.2 \
    php7.2-common \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-xmlreader \
    php7.2-zip \
    wget \
    git \
    vim \
    zip \
    unzip

RUN a2enmod http2 expires deflate rewrite session

WORKDIR /var/www/html/
RUN rm -rf index.html



RUN ls -ahl
RUN test -f index.html && rm index.html || true

RUN git clone https://fahn:2a806806979e1e8ae0bc51af93dd6bc6a9782098@github.com/fahn/rangliste.git .
RUN rm -rf .git* smarty/templates_c/*

RUN chmod -R 755 .
RUN chown -R www-data:www-data .

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

USER www-data
WORKDIR /var/www/html/

# install composer requiredments
## parse md-files
RUN composer require erusev/parsedown
## send mails
RUN composer require nette/mail
## read and write xsl-files
RUN composer require box/spout

USER root

# Ports
EXPOSE 80

# Volumes
VOLUME /var/www/html/inc/config.ini

ENTRYPOINT [ "/usr/sbin/apache2ctl" ]

CMD [ "-D", "FOREGROUND" ]
