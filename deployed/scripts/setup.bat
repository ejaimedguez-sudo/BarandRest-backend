@echo off
REM Windows batch setup script: install composer deps, generate key, migrate, create queue table
echo Running setup.bat...
set PHP="C:\xampp\php\php.exe"
if not exist %PHP% (
  echo PHP not found at %PHP%. Please install XAMPP and adjust path.
  exit /b 1
)

cd /d %~dp0\..

if not exist vendor (
  if not exist composer.phar (
    echo Downloading composer.phar...
    powershell -Command "Invoke-WebRequest -Uri https://getcomposer.org/composer-stable.phar -OutFile composer.phar"
  )
  %PHP% composer.phar install
) else (
  echo vendor/ exists, skipping composer install.
)

if not exist .env (
  copy .env.example .env
  %PHP% artisan key:generate --force
)

echo Creating storage link and reports folder...
%PHP% artisan storage:link 2>nul
if not exist storage\app\reports mkdir storage\app\reports

echo Creating queue table and running migrations...
%PHP% artisan queue:table 2>nul
%PHP% artisan migrate --force

echo Setup complete. Run: %PHP% artisan queue:work to start worker.
