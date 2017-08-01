FROM diginc/pi-hole:alpine_v3.1

# Install php & composer.
ENV COMPOSER_HOME /usr/local/composer
RUN apk add --update php7 php7-phar php7-json php7-openssl php7-mbstring php7-curl \
  && rm -rf /var/cache/apk/* \
  && EXPECTED_SIGNATURE=$(curl https://composer.github.io/installer.sig) \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');") \
  && [ "$EXPECTED_SIGNATURE" == "$ACTUAL_SIGNATURE" ] \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

# Copy config overrides.
COPY etc /etc

# Install MiCloud CLI.
ADD MiCloud-CLI /usr/local/micloud
RUN cd /usr/local/micloud \
  && composer install \
  && cd /usr/local/bin \
  && ln -s /usr/local/micloud/micloud.php micloud

