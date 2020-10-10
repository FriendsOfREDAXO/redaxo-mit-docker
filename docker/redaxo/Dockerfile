FROM friendsofredaxo/demo:base

# copy custom configs
COPY php.ini /usr/local/etc/php/
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY msmtprc /etc/msmtprc
COPY .mailrc ~/.mailrc

# generate SSL cert for testing purposes
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/CN=localhost"

# enable apache modules
RUN a2enmod expires headers rewrite ssl
RUN a2ensite default-ssl

# use noninteractive frontend to install extionsions
# http://manpages.org/debconf/
ENV DEBIAN_FRONTEND noninteractive

# install extensions
RUN apt-get update -q && apt-get install -qy \
       msmtp \
       msmtp-mta \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# enable blackfire
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

# start apache
CMD ["apache2-foreground"]
