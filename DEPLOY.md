# DentalFlow SaaS - Deployment Guide

## Prerequisites

- PHP 8.3+
- PostgreSQL 14+
- Node.js 20+
- Composer 2.0+
- Redis (optional, for caching/queues)

## Manual Deployment

### 1. Clone and Setup

```bash
git clone https://github.com/rommelescorihuela/dentalflowsaas.git
cd dentalflowsaas

# Install dependencies (production mode)
composer install --no-dev --optimize-autoloader

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 2. Configure Environment

Edit `.env` with your production values:

```env
APP_NAME=DentalFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dentalflow_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

TENANCY_CENTRAL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### 3. Database Setup

```bash
# Create database
createdb dentalflow_prod

# Run migrations
php artisan migrate --force

# Seed initial data (optional)
php artisan db:seed --class=TenantSeeder
```

### 4. Build Assets

```bash
npm ci
npm run build
```

### 5. Final Setup

```bash
# Create admin user
php artisan make:filament-user

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

### 6. Queue Worker

```bash
# Start queue worker (supervisor recommended)
php artisan queue:work --tries=3
```

### 7. Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/html/dentalflowsaas/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Docker Deployment

### Build Image

```bash
docker build -t dentalflow:latest .
```

### Run Container

```bash
docker run -d \
    --name dentalflow \
    -p 9000:9000 \
    -e APP_ENV=production \
    -e DB_HOST=your-db-host \
    -e DB_DATABASE=dentalflow_prod \
    -e DB_USERNAME=your_db_user \
    -e DB_PASSWORD=your_secure_password \
    dentalflow:latest
```

### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "9000:9000"
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - DB_DATABASE=dentalflow_prod
      - DB_USERNAME=dentalflow
      - DB_PASSWORD=secret
    depends_on:
      - db
      - redis

  db:
    image: postgres:15
    environment:
      - POSTGRES_DB=dentalflow_prod
      - POSTGRES_USER=dentalflow
      - POSTGRES_PASSWORD=secret
    volumes:
      - pgdata:/var/lib/postgresql/data

  redis:
    image: redis:7-alpine

volumes:
  pgdata:
```

## CI/CD Deployment

### GitHub Actions

The project includes `.github/workflows/ci.yml` which runs on every push/PR:

1. **Tests**: Runs full test suite against PostgreSQL
2. **Code Quality**: PHPStan/Larastan analysis
3. **Security**: Composer security audit

### Forge/Vapor Deployment

Set `COMPOSER_FLAGS=--no-dev` in your deployment script:

```bash
cd /home/forge/dentalflowsaas
git pull origin main
composer install $COMPOSER_FLAGS --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Post-Deployment Checklist

- [ ] Health check returns 200: `curl https://yourdomain.com/up`
- [ ] Admin panel accessible: `https://yourdomain.com/admin`
- [ ] Queue worker running
- [ ] SSL certificate configured
- [ ] Database backups scheduled
- [ ] Error monitoring configured (Sentry, etc.)
- [ ] Run `php artisan diagnostic:all --skip-tests`

## Rollback Procedure

```bash
# Rollback last migration
php artisan migrate:rollback --force

# Restore previous code version
git checkout <previous-commit>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache
```

## Monitoring

### Logs
```bash
# View application logs
tail -f storage/logs/laravel.log

# View queue logs
php artisan queue:work --verbose
```

### Health Check
```bash
curl -f https://yourdomain.com/up
```

### Diagnostics
```bash
php artisan diagnostic:all --skip-tests
```
