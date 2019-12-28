FROM ubuntu:18.04

LABEL MAINTAINER="Stefan Metzner <stmetzner@gmail.com>"
LABEL VERSION="0.1"

# install apache2 and shinx
RUN apt-get -qq update && apt-get -f install -y python3-sphinx apache2 python3-pip

# install theme
RUN pip install sphinx_theme
COPY /doc/* /doc/

RUN sphinx-build -b html /doc/ /var/www/html/

EXPOSE 80

CMD apache2 -DFOREGROUND