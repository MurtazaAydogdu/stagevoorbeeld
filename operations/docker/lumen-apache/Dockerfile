FROM docker-registry.bjoola.nl/microservicetransactions/lumen:microservicetransactions

MAINTAINER Murtaza Aydogdu <murtaza@digitalefactuur.nl>

# add multiverse
RUN sed -i_orig \
    -e "s/xenial main restricted/xenial main restricted multiverse/g" \
    -e "s/xenial-updates main restricted/xenial-updates main restricted multiverse/g" \
    /etc/apt/sources.list

# install php-fpm and nginx
RUN apt-get update -y \
    && apt-get install -y \
    apache2 \
    libapache2-mod-php7.2 \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log \
    && ln -sf /proc/self/fd/1 /var/log/apache2/error.log \
    && a2enmod rewrite \
    && a2ensite 000-default