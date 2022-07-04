FROM php:8.1-fpm

ARG DOCKER_PHP_EXT=1.5.29
ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/${DOCKER_PHP_EXT}/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN \
    apt-get update; \
    apt-get install -y --fix-missing \
        apt-utils \
        bash-completion \
        g++ \
        gcc \
        git \
        gnupg \
        make \
        nano \
        unzip \
    ; \
    install-php-extensions \
        amqp \
        apcu \
        bcmath \
        decimal \
        intl \
        mcrypt \
        opcache \
        pdo_mysql \
        xmldiff \
        xmlrpc \
        xsl \
        zip \
    ; \
    apt-get autoremove -y; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    truncate -s 0 /var/log/*log;

# Setting up PHP's timezone
ARG TZ
RUN echo "date.timezone = ${TZ}" > ${PHP_INI_DIR}/conf.d/timezone.ini

# Install APCu & OPCache
ARG APCU_VERSION=5.1.21
RUN pecl install apcu-${APCU_VERSION}; \
	pecl clear-cache; \
	docker-php-ext-enable apcu opcache;

# Install composer
RUN \
    curl -slL https://getcomposer.org/composer-2.phar -o /usr/local/bin/composer; \
    chmod +x /usr/local/bin/composer;

# Create user
ARG UID
RUN \
    useradd -ms /bin/bash --uid ${UID} docker; \
    usermod -a -G www-data docker;
COPY ./php/.bashrc /home/docker/.bashrc
RUN \
    chown -R docker:docker /home/docker; \
    chown -R docker /var/www/html;
USER docker

# Setting up user
RUN \
    echo "set linenumbers" > /home/docker/.nanorc

WORKDIR /var/www/html
