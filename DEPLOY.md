# DentalFlow SaaS — Guía de Despliegue

---

## Requisitos Previos

- PHP 8.3+
- PostgreSQL 14+
- Node.js 20+
- Composer 2.0+
- Redis (opcional, para caché/colas)

---

## Despliegue Manual

### 1. Clonar e Instalar

```bash
git clone https://github.com/rommelescorihuela/dentalflowsaas.git
cd dentalflowsaas

# Dependencias (modo producción)
composer install --no-dev --optimize-autoloader

# Entorno
cp .env.example .env
php artisan key:generate
```

### 2. Configurar .env

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

# Dominios centrales para tenancy
TENANCY_CENTRAL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### 3. Base de Datos

```bash
createdb dentalflow_prod
php artisan migrate --force

# Datos iniciales (opcional)
php artisan db:seed --class=TenantSeeder
php artisan db:seed --class=ProcedurePriceSeeder
```

### 4. Assets

```bash
npm ci
npm run build
```

### 5. Finalizar

```bash
php artisan make:filament-user                                      # Usuario admin
php artisan config:cache && php artisan route:cache && php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

### 6. Colas

```bash
php artisan queue:work --tries=3   # Recomendado: usar supervisor
```

---

## Nginx (Wildcard Subdomain)

DentalFlow usa subdominios para identificar tenants en producción (`{clinic}.yourdomain.com`).

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

### DNS

```
*.yourdomain.com    A    YOUR_SERVER_IP
yourdomain.com      A    YOUR_SERVER_IP
```

### SSL (Let's Encrypt Wildcard)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot certonly --manual --preferred-challenges dns -d "yourdomain.com" -d "*.yourdomain.com"
```

Agregar al bloque Nginx:
```nginx
listen 443 ssl http2;
ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
```

---

## Registrar Dominios de Tenant

Después del despliegue, registrar los subdominios en la tabla `domains`:

```bash
php artisan tinker
>>> $clinic = \App\Models\Clinic::find('clinic1');
>>> $clinic->domains()->create(['domain' => 'clinic1.yourdomain.com']);
```

O re-ejecutar el TenantSeeder (agrega dominios de producción automáticamente):
```bash
php artisan db:seed --class=TenantSeeder
```

---

## Docker

### Build

```bash
docker build -t dentalflow:latest .
```

### Run

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

---

## CI/CD (Forge / Vapor)

```bash
cd /home/forge/dentalflowsaas
git pull origin main
composer install $COMPOSER_FLAGS --optimize-autoloader   # COMPOSER_FLAGS=--no-dev
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Checklist Post-Deploy

- [ ] Health check: `curl https://yourdomain.com/up`
- [ ] Admin panel: `https://yourdomain.com/admin`
- [ ] Clinic panel: `https://clinic1.yourdomain.com/app/login`
- [ ] Wildcard DNS configurado
- [ ] Nginx configurado con wildcard subdomain
- [ ] Tenant domains registrados en tabla `domains`
- [ ] Queue worker corriendo (supervisor)
- [ ] SSL wildcard configurado
- [ ] Backups de base de datos programados
- [ ] `php artisan diagnostic:all --skip-tests` sin errores

---

## Rollback

```bash
php artisan migrate:rollback --force
git checkout <previous-commit>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache
```

---

## Monitoreo

```bash
# Logs
tail -f storage/logs/laravel.log

# Colas
php artisan queue:work --verbose

# Health check
curl -f https://yourdomain.com/up

# Diagnóstico
php artisan diagnostic:all --skip-tests
```
