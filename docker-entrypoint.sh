#!/bin/sh
set -e

php artisan config:cache
php artisan migrate --force
php artisan db:seed --force
php artisan route:cache
php artisan view:cache

# public/storage is gitignored (standard Laravel practice) and never gets
# created on its own — without this, every uploaded resource's download
# link 404s because the symlink target simply doesn't exist in the
# container. Safe to run on every boot; it's a no-op if already linked.
php artisan storage:link || true

# Render assigns $PORT dynamically at runtime, so the nginx config can't
# hardcode it — bake it into the config from the template on every boot.
# Restricting envsubst to just $PORT keeps nginx's own $host/$http_upgrade/
# etc. variables untouched (envsubst would otherwise blank them out).
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

exec supervisord -c /etc/supervisord.conf