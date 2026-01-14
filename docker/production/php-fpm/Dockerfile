#Builder stage
FROM php:8.4-fpm as builder

# Update repositories and install dependencies
# -y: Assume yes to all prompts
# --no-install-recommends: Skip optional dependencies to keep image slim
# apt-get autoremove: Cleanup unnecessary build-time dependencies
# apt-get clean & rm: Remove cached package lists and temporary files to reduce image size
# docker-php-ext-install: Helper script to compile and configure PHP extensions from source
# -j$(nproc): Enables parallel processing by utilizing all available CPU cores 
RUN apt-get update &&   apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
	&& docker-php-ext-configuration gd --with-freetype --with-jpeg \
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

# Set the default working directory for all subsequent instructions
# Instructions like RUN, COPY, and ADD will be executed in this context
# Automatically creates the directory if it does not exist
WORKDIR /var/www

#copy all files and folders from backend folder to destination inside container
COPY ./backend /var/www/

# Install Composer and project dependencies
# --no-dev: Exclude development packages for a slimmer production image
# --optimize-autoloader: Optimize class loading performance
# --no-interaction: Automated install without user prompts
# --no-progress: Keep build logs clean by hiding progress bars
# --prefer-dist: Install packages from archives for faster downloads
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

#Production stage
FROM php:8.4-fpm as production

# Install only runtime libraries needed in production
# libfcgi-bin and procps are required for the php-fpm-healthcheck script
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libfcgi-bin \
    procps \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Download and install php-fpm health check script
# curl -o <destination>: download script to destination
# chmod +x: provides execute permission for download script
RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Copy the initialization script
COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy the initial storage structure
COPY ./backend/storage /var/www/storage-init

# Copy PHP extensions and libraries from the builder stage
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

# Use the recommended production PHP configuration
# $PHP_INI_DIR: Pre-defined env variable in the official php:8.4-fpm image
# Activates production settings for better performance and security
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Enable PHP-FPM status page by modifying zz-docker.conf with sed
# sed -i: In-place edit of zz-docker.conf to inject the status path
# This is required for the php-fpm-healthcheck script to function
RUN sed -i '/\[www\]/a pm.status_path = /status' /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy the application code and dependencies from the build stage
COPY --from=builder /var/www /var/www

# Set working directory
WORKDIR /var/www

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www

# Switch to the non-privileged user to run the application
USER www-data

# Change the default command to run the entrypoint script
# Ensure entrypoint alway called whenever container start
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 9000
EXPOSE 9000

# Start php-fpm server
CMD ["php-fpm"]