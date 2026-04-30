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

### 7. Nginx Configuration (with Wildcard Subdomain Support)

**Important:** DentalFlow uses subdomain-based tenant identification in production. Each clinic accesses the app via `{clinic}.yourdomain.com`.

#### Option A: Wildcard Subdomain (Recommended)

```nginx
server {
    listen 80;
    server_name *.yourdomain.com yourdomain.com;
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

#### Option B: Explicit Subdomains

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com clinic1.yourdomain.com clinic2.yourdomain.com;
    root /var/www/html/dentalflowsaas/public;

    # ... rest of configuration same as above
}
```

### 8. DNS Configuration

You need to configure DNS to route subdomains to your server:

#### Option A: Wildcard DNS Record (Recommended)

Add a wildcard A record in your DNS provider:

```
*.yourdomain.com    A    YOUR_SERVER_IP
yourdomain.com      A    YOUR_SERVER_IP
```

#### Option B: Individual DNS Records

```
yourdomain.com      A    YOUR_SERVER_IP
clinic1.yourdomain.com    A    YOUR_SERVER_IP
clinic2.yourdomain.com    A    YOUR_SERVER_IP
```

### 9. SSL Certificate (Let's Encrypt with Wildcard)

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Get wildcard certificate (requires DNS challenge)
sudo certbot certonly --manual --preferred-challenges dns \
    -d "yourdomain.com" -d "*.yourdomain.com"

# Update Nginx to use SSL
```

Add to your Nginx server block:
```nginx
listen 443 ssl http2;
ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
```

### 10. Auto-Creación de Subdominios vía cPanel (Opcional pero Recomendado)

DentalFlow puede crear automáticamente los subdominios en cPanel cuando agregas una nueva clínica desde el panel admin.

#### Paso 1: Generar un API Token en cPanel

1. Inicia sesión en cPanel
2. Ve a **Security > Manage API Tokens**
3. Haz clic en **Generate Token**
4. Dale un nombre (ej: `dentalflow-api`)
5. Copia el token generado (solo se muestra una vez)

#### Paso 2: Configurar `.env`

```env
CPANEL_ENABLED=true
CPANEL_URL=https://dentalflow.digitalwebsolution.info:2083
CPANEL_USERNAME=tu_usuario_cpanel
CPANEL_TOKEN=el_token_que_generaste
CPANEL_ROOT_DOMAIN=dentalflow.digitalwebsolution.info
```

#### Paso 3: Verificar conexión

```bash
php artisan tinker
>>> app(\App\Services\CpanelService::class)->createSubdomain('test', 'dentalflow.digitalwebsolution.info')
# Debe retornar true y crear el subdominio test.dentalflow.digitalwebsolution.info
```

#### ¿Cómo funciona?

- Cuando creas una clínica desde `/admin/app/clinics/create`, el sistema automáticamente:
  1. Crea el subdominio `{id}.dentalflow.digitalwebsolution.info` en cPanel
  2. Registra el dominio en la tabla `domains`
  3. El subdominio apunta automáticamente a la carpeta `public/` del proyecto

- Cuando eliminas una clínica, el subdominio se elimina automáticamente

#### URLs resultantes:
- Clínica 1: `https://clinic1.dentalflow.digitalwebsolution.info/app/login`
- Clínica 2: `https://clinic2.dentalflow.digitalwebsolution.info/app/login`

### 11. Register Tenant Domains (si no usas cPanel auto-creación)

After deploying, you need to register the production subdomains in the `domains` table:

```bash
# Option 1: Re-run TenantSeeder (will add production domains automatically)
php artisan db:seed --class=TenantSeeder

# Option 2: Manually add domains via tinker
php artisan tinker
>>> $clinic = \App\Models\Clinic::find('clinic1');
>>> $clinic->domains()->create(['domain' => 'clinic1.yourdomain.com']);
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
- [ ] Clinic panel accessible: `https://clinic1.yourdomain.com/app/login`
- [ ] Wildcard DNS configured: `*.yourdomain.com` → server IP
- [ ] Nginx configured with wildcard subdomain (`*.yourdomain.com`)
- [ ] Tenant domains registered in `domains` table
- [ ] Queue worker running
- [ ] SSL certificate configured (wildcard recommended)
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
