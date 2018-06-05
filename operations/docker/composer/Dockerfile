FROM docker-registry.bjoola.nl/microservicetransactions/lumen:microservicetransactions

MAINTAINER Brandon van Wijk <brandon@digitalefactuur.nl>

WORKDIR /tmp

RUN apt-get update -y \
    &&  apt-get install -y \
    git \
    zip \
    unzip \
    php7.2-zip \
    php7.2-mbstring \
    &&  curl -sS https://getcomposer.org/installer | php \
    &&  mv composer.phar /usr/local/bin/composer \
    &&  composer self-update \
    &&  apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN mkdir -p /data/www
VOLUME ["/data"]
WORKDIR /data/www
ENV COMPOSER_HOME=/data

ENTRYPOINT ["/sbin/my_init","--","composer"]
CMD ["--help"]