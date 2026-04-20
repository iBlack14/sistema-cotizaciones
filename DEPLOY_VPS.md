# Despliegue tradicional en Coolify (sin Docker)

Esta guia es para desplegar Laravel en Coolify usando buildpacks/Nixpacks (modo tradicional), sin mantener un `Dockerfile` propio para el deploy.

## 1) Requisitos del servidor

- Coolify instalado y operativo en tu VPS
- Una base de datos MySQL/MariaDB (en el mismo Coolify o externa)
- Repositorio Git del proyecto accesible por Coolify

## 2) Crear el recurso en Coolify

1. En Coolify, crea un recurso tipo **Application**.
2. Conecta tu repositorio y selecciona la rama a desplegar.
3. En **Build Pack**, usa deteccion automatica (Nixpacks).
4. Define el **Puerto** de la app en `80` (si usas servidor web interno de PHP, usa `8000` y ajusta reverse proxy).
5. Dominio: agrega tu dominio final (por ejemplo `cotizaciones.tudominio.com`).

## 3) Variables de entorno (Environment)

Configura al menos estas variables:

```env
APP_NAME=COTIZACIONES
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_KEY=base64:GENERADA_EN_PRODUCCION

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=host_de_tu_db
DB_PORT=3306
DB_DATABASE=tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_clave

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

# WhatsApp integration mode
# browser = abre wa.me en el navegador
# baileys = usa API externa (microservicio Node separado)
WHATSAPP_MODE=browser
WHATSAPP_COUNTRY_CODE=51
WHATSAPP_API_URL=
WHATSAPP_API_KEY=
WHATSAPP_INSTANCE_NAME=viacomunicativa
WHATSAPP_API_TIMEOUT=15
```

Notas:
- Si Coolify te provee DB administrada, usa las credenciales que te entrega el recurso DB.
- No subas secretos al repo: todo va en variables de Coolify.

## 4) Comando de build (Build Command)

Usa este comando en Coolify:

```bash
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader && npm ci && npm run build
```

## 5) Comando de inicio (Start Command)

Para despliegue tradicional simple en Coolify:

```bash
php artisan migrate --force && php artisan storage:link || true && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=80
```

Si prefieres no migrar automaticamente en cada inicio, quita `php artisan migrate --force` y ejecútalo manual al desplegar.

## 6) Directorios con permisos de escritura

Laravel necesita escritura en:

- `storage/`
- `bootstrap/cache/`

Coolify normalmente lo maneja, pero si algo falla en runtime, revisa permisos/owner del contenedor.

## 7) Primer deploy (checklist)

1. Deploy inicial desde Coolify.
2. Verifica que la build termine sin errores.
3. Abre logs de runtime y confirma que no existan errores de `APP_KEY`, DB o permisos.
4. Abre la URL y valida login/home.
5. Revisa que se creen las tablas con migraciones.

## 8) Comandos utiles post-deploy

Para ejecutar dentro del contenedor/app:

```bash
php artisan migrate:status
php artisan about
php artisan optimize:clear
php artisan config:cache
```

## 9) Solucion de problemas comunes

- Error `No application encryption key has been specified.`  
  Define `APP_KEY` (o genera una y guardala en variables de Coolify).

- Error de conexion DB (`SQLSTATE[HY000] [2002]`)  
  Revisa `DB_HOST`, puerto, usuario, password y red entre app y base.

- Pantalla 500 tras deploy  
  Ejecuta `php artisan optimize:clear` y revisa logs de runtime.

## 10) WhatsApp en este proyecto

El sistema abre WhatsApp por enlace `https://wa.me/...`.

- No necesita Evolution API para el flujo normal de envio por navegador.
- No necesita QR en el VPS.

## 11) Migrar a Baileys (proyecto separado)

Si luego deseas usar sesiones reales (QR + envio desde servidor), configura:

- `WHATSAPP_MODE=baileys`
- `WHATSAPP_API_URL` apuntando al microservicio Node
- `WHATSAPP_API_KEY` compartida con el servicio
- `WHATSAPP_INSTANCE_NAME` con el nombre de instancia

Revisa `BAILEYS_ROADMAP.md` para el plan de separación por fases.
