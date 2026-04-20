# Despliegue en VPS

## Requisitos

- PHP 8.2 o superior
- Composer 2
- Node.js 20 o superior
- MySQL o MariaDB
- Nginx o Apache apuntando a `public/`

## Variables importantes

En tu `.env` configura al menos:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_clave

WHATSAPP_COUNTRY_CODE=51
```

## Pasos

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permisos

Da permisos de escritura a:

- `storage/`
- `bootstrap/cache/`

## WhatsApp

El sistema ahora abre WhatsApp directo con enlaces `https://wa.me/...`.

- No necesita Evolution API
- No necesita QR ni instancia en el servidor
- El envio ocurre desde el navegador del usuario, no desde el VPS
