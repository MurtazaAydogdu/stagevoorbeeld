FROM docker-registry.bjoola.nl/microservicetransactions/lumen:microservicetransactions

MAINTAINER Murtaza Aydogdu <murtaza@digitalefactuur.nl>

RUN apt-get update -y \
    && apt-get install -y \
    php-ast \
    patch \
    php7.2-xdebug \
    && phpenmod -s cli xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENTRYPOINT ["/sbin/my_init", "--quiet", "--"]
CMD ["php"]