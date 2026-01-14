#!/bin/sh

# -e: Exit immediately if any command fails
set -e

# $(): Execute a command and use its output as a value
# -A: List all files and directories, including hidden ones (except . and ..)
# /.: Copy all contents inside the directory (including hidden files)
if [  ! "$(ls -A /var/www/storage)" ]; then
	echo "Initializing storage directory..."
	cp -R /var/www/storage-init/. /var/www/storage
	chown -R www-data:www-data /var/www/storage
fi

# Remove storage-init directory
rm -rf /var/www/storage-init

# Run Laravel migrations
# --force: run command without wating confirm
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache

# Run the default command
exec "$@"