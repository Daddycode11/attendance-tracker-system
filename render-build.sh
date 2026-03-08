#!/usr/bin/env bash
# Render.com build script for Laravel
set -e

echo "==> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing Node dependencies & building assets..."
npm ci
npm run build

echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding database..."
php artisan db:seed --force

echo "==> Build complete!"
