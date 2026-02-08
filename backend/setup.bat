@echo off
REM Batch setup script for BarAndRest (Windows)
SET ROOT=%~dp0
cd /d %ROOT%

IF NOT EXIST .env (
  copy .env.example .env
  echo Created .env from .env.example
)

SET PHP=C:\\xampp\\php\\php.exe
IF NOT EXIST "%PHP%" (
  SET PHP=php
)

IF EXIST composer.phar (
  SET COMPOSER=%PHP% composer.phar
) ELSE (
  SET COMPOSER=composer
)

echo Running composer install...
%COMPOSER% install --no-interaction --prefer-dist

echo Generating app key
%PHP% artisan key:generate --ansi

echo Running migrations
%PHP% artisan migrate --force

echo Ensuring queue table
%PHP% artisan queue:table || echo queue:table skipped
%PHP% artisan migrate --force

echo Seeding test data (if available)
%PHP% artisan db:seed --class=TestDataSeeder --force || echo Seeder skipped

if not exist storage\app\reports mkdir storage\app\reports

echo Creating storage link
%PHP% artisan storage:link || echo storage:link skipped

echo Setup complete.
echo Run: %PHP% artisan reports:daily && %PHP% artisan queue:work --once
