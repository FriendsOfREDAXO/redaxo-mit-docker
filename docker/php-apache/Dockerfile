FROM php:7.1-apache

# copy custom configs
COPY php.ini /usr/local/etc/php/
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# enable apache modules
RUN a2enmod expires headers rewrite ssl

# install extensions
RUN apt-get update -q && apt-get install -qy \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql

# set ENVs
ENV REDAXO_VERSION=5.3.0 REDAXO_SHA=c1d3dcc2401b39cd9d32ea7de2ea358683ce81ceb8a5845f7a79937adcd5048e

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
ENTRYPOINT ["docker-entrypoint.sh"]

# start apache
CMD ["apache2-foreground"]
