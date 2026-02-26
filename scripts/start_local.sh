#!/usr/bin/env bash
set -euo pipefail
echo "Starting Laravel server and queue worker in background"
php artisan serve --host=127.0.0.1 --port=8000 &
php artisan queue:work --sleep=3 --tries=3 &
echo "Server started at http://127.0.0.1:8000"
