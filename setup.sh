#! /bin/bash

cd tmp
export DEBIAN_FRONTEND=noninteractive
export locale-gen=en_US.UTF-8
export LANG=en_US.UTF-8
export LANGUAGE=en_US:en
export LC_ALL=en_US.UTF-8

apt-get update \
    && apt-get install -y wget gnupg2 curl git zip \
    unzip locales software-properties-common vim

apt-get update; \
  apt-get install curl php-dev php-pear libssh2-1-dev -y; \
  curl http://pecl.php.net/get/ssh2-1.2.tgz -o ssh2.tgz; \
  printf "\n" | pecl install ssh2 ssh2.tgz; \
  rm -rf ssh2.tgz

add-apt-repository --yes ppa:ondrej/php; \
    wget http://archive.ubuntu.com/ubuntu/pool/main/o/openssl1.0/libssl1.0.0_1.0.2n-1ubuntu5.8_amd64.deb; \
    dpkg -i libssl1.0.0_1.0.2n-1ubuntu5.8_amd64.deb; \
    apt-get update; apt-get install -y --no-install-recommends \
    gcc make acl libpq5 apache2 libpq-dev postfix \
    php7.4 libapache2-mod-php7.4 php7.4-bcmath php7.4-zip \
    php7.4-common php7.4-mbstring php7.4-xml php7.4-imap \
    php7.4-fpm php7.4-cli php7.4-pgsql php7.4-sqlite3 php7.4-redis \
    php7.4-apcu php7.4-intl php7.4-json php7.4-gd \
    php7.4-memcached php-ssh2 openssh-server php7.4-curl php7.4-mcrypt postgresql-client-12;

a2enmod proxy_fcgi setenvif && a2enconf php7.4-fpm && a2enmod php7.4 && a2enmod headers && update-alternatives --set php /usr/bin/php7.4 && service apache2 restart

phpenmod mcrypt && \
  rm -rf /var/lib/apt/lists/* && \
  curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer


curl https://phar.phpunit.de/phpunit-3.7.28.phar > phpunit.phar && \
  chmod +x phpunit.phar && \
  mv phpunit.phar /usr/local/bin/phpunit
