FROM php:7.3.8-fpm

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --fix-missing \
        apt-utils \
        gnupg \
        vim \
        unzip \
        git \
    && echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add - \
    && apt-get update \
    && apt-get install -y \
        zlib1g-dev \
        libzip-dev \
    && docker-php-ext-install zip

ARG UID
RUN \
    useradd -ms /bin/bash --uid ${UID} githubgui \
    && usermod -a -G www-data githubgui \
    && chown -R githubgui /var/www/html

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /var/www/html

USER githubgui
