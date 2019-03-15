FROM php:7.3-apache

# copy custom configs
COPY php.ini /usr/local/etc/php/
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY ssmtp.conf /etc/ssmtp/

# generate SSL cert for testing purposes
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/CN=localhost"

# enable apache modules
RUN a2enmod expires headers rewrite ssl
RUN a2ensite default-ssl

# install extensions
RUN apt-get update -q && apt-get install -qy \
       libfreetype6-dev \
       libjpeg62-turbo-dev \
       libmcrypt-dev \
       libpng-dev \
       libzip-dev \
       ssmtp \
       unzip \
       dos2unix \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

# decide which REDAXO version to use
# generate checksum like this: `curl -Ls https://github.com/redaxo/redaxo/releases/download/5.6.4/redaxo_5.6.4.zip | shasum`
ENV REDAXO_VERSION=5.7.0 REDAXO_SHA=68e11d552ad340e374552e0367292d1f9cb49b87

# fetch REDAXO, validate checksum and extract to tmp folder
RUN set -e; \
    mkdir /tmp/redaxo; \
    curl -Ls -o /tmp/redaxo/redaxo_${REDAXO_VERSION}.zip https://github.com/redaxo/redaxo/releases/download/${REDAXO_VERSION}/redaxo_${REDAXO_VERSION}.zip; \
    echo "${REDAXO_SHA} */tmp/redaxo/redaxo_${REDAXO_VERSION}.zip" | shasum -c -a 256; \
    unzip -oq /tmp/redaxo/redaxo_${REDAXO_VERSION}.zip -d /tmp/redaxo/src; \
    rm -f /tmp/redaxo/redaxo_${REDAXO_VERSION}.zip;

# copy REDAXO configs and helpers
COPY default.config.yml demos.yml docker-redaxo.php /tmp/redaxo/

# copy REDAXO setup script and run setup
COPY docker-entrypoint.sh /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# start apache
CMD ["apache2-foreground"]
