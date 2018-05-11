FROM diginc/pi-hole:debian_v3.3.1

# Install php & composer.
ENV COMPOSER_HOME /usr/local/composer
RUN set -eux; \
  apt-get update; \
  apt-get install -y php php-phar php-json php-mbstring php-curl; \
  apt-get clean; \
  EXPECTED_SIGNATURE=$(curl https://composer.github.io/installer.sig); \
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
  ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');"); \
  [ "$EXPECTED_SIGNATURE" == "$ACTUAL_SIGNATURE" ]; \
  php composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
  rm composer-setup.php

# Copy config overrides.
COPY etc /etc

# Install MiHole CLI.
ADD MiHole-CLI /usr/local/mihole
RUN cd /usr/local/mihole \
  && composer install \
  && cd /usr/local/bin \
  && ln -s /usr/local/mihole/mihole.php mihole
