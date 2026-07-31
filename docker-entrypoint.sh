#!/bin/sh
set -e

php artisan config:cache
php artisan migrate --force
php artisan route:cache
php artisan view:cache

# Render assigns $PORT dynamically at runtime, so the nginx config can't
# hardcode it — bake it into the config from the template on every boot.
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

exec supervisord -c /etc/supervisord.conf