#build stage
FROM php:8.4-cli AS builder

RUN apt-get update &&   apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
	libfreetype-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    opcache \
    intl \
    zip \
    bcmath \
    soap \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --no-progress --prefer-dist

# development stage
FROM php:8.4-cli AS development

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER
ARG XDEBUG_LOG=/dev/stdout
ARG XDEBUG_LOG_LEVEL=0

RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=${XDEBUG_LOG}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=${XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
fi

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --from=builder /var/www /var/www

WORKDIR /var/www

# Add ARGs for syncing permissions
ARG UID=1000
ARG GID=1000

# Modified UID/GID for  existed user www-data  
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Switch back to the non-privileged user to run the application
USER www-data

CMD ["bash"]