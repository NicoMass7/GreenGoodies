FROM php:8.3-fpm

# Installation des dépendances

RUN apt update && apt install -y curl git wget dpkg

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin
RUN chmod +x /usr/local/bin/install-php-extensions

# Extensions recommandées par Symfony
# note : pour ce projet, redis est facultatif
RUN install-php-extensions opcache iconv soap
RUN install-php-extensions zip intl fileinfo
RUN install-php-extensions pdo redis mysqli pdo_mysql
RUN install-php-extensions gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Définir le répertoire de travail par défaut
WORKDIR /var/www