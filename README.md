# Sistema de Cotizaciones

Aplicacion Laravel para gestionar cotizaciones, dominios, mensajes, correo y envios por WhatsApp.

## Stack

- PHP 8.2+
- Laravel 12
- MySQL o MariaDB
- Node.js 20+
- Vite
- Tailwind CSS

## Funciones principales

- Gestion de cotizaciones
- Gestion de dominios
- Plantillas de mensajes
- Envio de correos
- Exportacion a PDF y Word
- Envio por WhatsApp en modo directo por navegador

## WhatsApp

Este proyecto ya no depende de una API externa para WhatsApp.

El envio funciona abriendo `https://wa.me/...` desde el navegador del usuario:

- no necesita Evolution API
- no necesita QR en el servidor
- no necesita una sesion persistente en el VPS
- el VPS no envia mensajes por su cuenta

## Instalacion local

1. Clona el proyecto.
2. Instala dependencias de PHP.
3. Instala dependencias de Node.
4. Crea tu archivo `.env`.
5. Genera la clave.
6. Ejecuta migraciones.
7. Compila assets.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm run build
php artisan serve
```

## Variables de entorno

Usa `.env.example` como base.

Las variables importantes son:

```env
APP_NAME=COTIZACIONES
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_cotizaciones
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hola@example.com"
MAIL_FROM_NAME="COTIZACIONES"

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=file

WHATSAPP_COUNTRY_CODE=51
SYSTEM_MAINTENANCE_KEY=
```

## Pruebas

```bash
php artisan test
```

## Subir a GitHub

Si aun no inicializaste git:

```bash
git init
git add .
git commit -m "Primer commit"
git branch -M main
git remote add origin https://github.com/TU-USUARIO/TU-REPO.git
git push -u origin main
```

`.env`, `vendor`, `node_modules` y archivos de build locales ya estan ignorados en `.gitignore`.

## Despliegue con Coolify

Puedes desplegar este proyecto como aplicacion PHP/Laravel desde un repositorio GitHub.

### 1. Crear el proyecto en Coolify

1. Sube este repositorio a GitHub.
2. En Coolify crea un nuevo recurso desde GitHub.
3. Selecciona este repositorio.
4. Usa la rama principal.
5. Define el dominio.

### 2. Variables recomendadas en Coolify

Configura al menos:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_KEY=base64:GENERADA_POR_LARAVEL

DB_CONNECTION=mysql
DB_HOST=tu-host
DB_PORT=3306
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

MAIL_MAILER=smtp
MAIL_HOST=tu-host-smtp
MAIL_PORT=465
MAIL_USERNAME=tu-correo
MAIL_PASSWORD=tu-clave
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=tu-correo
MAIL_FROM_NAME="VC VIA COMUNICATIVA"

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=file
WHATSAPP_COUNTRY_CODE=51
SYSTEM_MAINTENANCE_KEY=una_clave_larga_y_privada
```

### 3. Build command

Si Coolify te pide comandos manuales, usa:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan package:discover --ansi
php artisan storage:link || true
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Start command

En la mayoria de despliegues Laravel con Nginx/Apache administrado por Coolify no necesitas un comando custom de arranque.

Si estas usando una imagen o configuracion que lo requiera, apunta siempre a la carpeta `public/`.

### 5. Post-deploy command

Usa esto despues de cada deploy:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Permisos

Verifica que la aplicacion tenga escritura en:

- `storage/`
- `bootstrap/cache/`

### 7. Queue worker

Si vas a usar colas, crea un worker separado en Coolify con:

```bash
php artisan queue:work --tries=1 --timeout=120
```

## Problemas comunes en Coolify

### Pantalla en blanco o error 500

Revisa:

- `APP_DEBUG=false`
- `APP_KEY` configurada
- permisos de `storage/` y `bootstrap/cache/`
- migraciones ejecutadas
- `APP_URL` correcto

### No cargan estilos

Revisa que `npm run build` haya corrido bien y que exista `public/build`.

### Error de base de datos

Revisa credenciales, host, puerto y si el contenedor/app puede conectarse al servicio MySQL.

### WhatsApp no "envia"

Eso es esperado: el sistema abre WhatsApp Web o la app del usuario con el mensaje listo. El envio final lo confirma el usuario en su propio WhatsApp.

## Archivos utiles

- [DEPLOY_VPS.md](c:\Users\via\Downloads\sistema cotizaciones\DEPLOY_VPS.md)
- [.env.example](c:\Users\via\Downloads\sistema cotizaciones\.env.example)
- [phpunit.xml.dist](c:\Users\via\Downloads\sistema cotizaciones\phpunit.xml.dist)

