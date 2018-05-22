FROM docker-registry.bjoola.nl/bjoola/base-image:0.0.3

WORKDIR /tmp

# Install PHP-CLI using APT
RUN add-apt-repository ppa:ondrej/php -y \
&&    apt-get update -y \
&&  apt-get install -y \
   php7.1 \
   php7.1-mysql \
   php7.1-bcmath \
&&  apt-get clean && rm -rf /var/lib/apt/lists/ */tmp/* /var/tmp/*

# Container entry point is PHP, default command is --help
ENTRYPOINT ["/sbin/my_init","--","php"]
CMD ["--help"]