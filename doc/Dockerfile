FROM ubuntu:18.04

LABEL MAINTAINER="Stefan Metzner <stmetzner@gmail.com>"
LABEL VERSION="0.1"

ARG DEBIAN_FRONTEND=noninteractive

# install apache2 and shinx
RUN apt-get -qq update && \
    apt-get upgrade -y && \
    apt-get -f install -y apt-utils python3-sphinx apache2 apache2-bin python3-pip && \
    rm -rf /var/lib/apt/cache

# install theme
RUN pip install sphinx_theme
COPY /doc/* /doc/

RUN sphinx-build -b html /doc/ /var/www/html/

EXPOSE 80

CMD apache2 -DFOREGROUND