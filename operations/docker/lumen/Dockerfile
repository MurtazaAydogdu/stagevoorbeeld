FROM docker-registry.bjoola.nl/bjoola/base-image:0.0.4


# Install PHP-CLI using APT
RUN add-apt-repository ppa:ondrej/php -y \
&&    apt-get update -y \
&&  apt-get install -y \
   php7.2 \
   php7.2-mysql \
   php7.2-bcmath \
   php7.2-xml \
   php7.2-dom \
   php7.2-mbstring \
&&  apt-get clean && rm -rf /var/lib/apt/lists/ */tmp/* /var/tmp/*


WORKDIR /data/www

ENV PATH=/data/www/vendor/bin:$PATH

# Container entry point is PHP, default command is --help
ENTRYPOINT ["/sbin/my_init","--","php"]
CMD ["--help"]