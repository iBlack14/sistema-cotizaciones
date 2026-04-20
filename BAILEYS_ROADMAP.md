# Roadmap de migración a Baileys (proyecto separado)

Este plan asume 2 proyectos distintos:

- `sistema-cotizaciones` (Laravel)
- `whatsapp-baileys-service` (Node + Baileys)

## Objetivo

Migrar de modo navegador (`wa.me`) a modo API (`baileys`) sin detener operación y con fallback seguro.

## Fase 1 - Contrato API estable

Definir endpoints mínimos del servicio Node:

- `GET /health`
- `GET /instance/:name/status`
- `GET /instance/:name/qr`
- `POST /instance/:name/send`
- `POST /instance/:name/disconnect`
- `GET /instance/:name/chats` (opcional)

Autenticación por header:

- `X-API-KEY: <secret>`

## Fase 2 - Servicio Node independiente

En repo `whatsapp-baileys-service`:

- Express + Baileys
- Manejo de instancia por `:name`
- Persistencia de auth state en volumen (`/data/baileys-auth`)
- Logs de conexión y envío

## Fase 3 - Deploy en Coolify (servicio Node)

- Crear aplicación aparte en Coolify
- Variables:
  - `PORT=3000`
  - `BAILEYS_API_KEY=...`
  - `SESSION_PATH=/data/baileys-auth`
- Montar volumen persistente para no perder sesión QR
- Verificar `GET /health`

## Fase 4 - Integración Laravel

Variables en Laravel:

- `WHATSAPP_MODE=baileys` (o `browser`)
- `WHATSAPP_API_URL=http://whatsapp-baileys-service:3000`
- `WHATSAPP_API_KEY=...`
- `WHATSAPP_INSTANCE_NAME=viacomunicativa`
- `WHATSAPP_COUNTRY_CODE=51`

El servicio `App\Services\WhatsAppService` debe:

- Consumir API en modo `baileys`
- Mantener fallback a `wa.me` en modo `browser`
- Responder errores claros cuando el API no esté disponible

## Fase 5 - Migración gradual en producción

1. Subir servicio Node y validar QR/status.
2. Mantener Laravel en `WHATSAPP_MODE=browser` durante pruebas.
3. Cambiar a `WHATSAPP_MODE=baileys`.
4. Validar envíos reales desde Soporte y Mensajes.
5. Monitorear logs y tiempos de respuesta 24h.

## Fase 6 - Criterios de salida

- Estado de instancia estable tras reinicios.
- Envíos exitosos desde UI Laravel.
- Registro en tabla `messages` funcionando.
- Sin errores de autenticación o timeout entre servicios.
