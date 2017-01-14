FROM diginc/pi-hole:alpine_ph-2.9.5_admin-1.4.4.2

# Install composer globally.
ENV COMPOSER_HOME /usr/local/composer
RUN apk add --update php5-phar \
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
  && composer install
