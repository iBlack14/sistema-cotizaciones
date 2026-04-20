<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $defaultCountryCode;

    protected string $mode;

    protected string $apiUrl;

    protected string $apiKey;

    protected string $defaultInstance;

    protected int $timeout;

    public function __construct()
    {
        $this->defaultCountryCode = preg_replace('/\D+/', '', (string) config('app.whatsapp_country_code', env('WHATSAPP_COUNTRY_CODE', '51')));
        $this->mode = strtolower((string) config('services.whatsapp_api.mode', 'browser'));
        $this->apiUrl = rtrim((string) config('services.whatsapp_api.url', ''), '/');
        $this->apiKey = (string) config('services.whatsapp_api.api_key', '');
        $this->defaultInstance = (string) config('services.whatsapp_api.instance_name', 'viacomunicativa');
        $this->timeout = (int) config('services.whatsapp_api.timeout', 15);

        if ($this->defaultCountryCode === '') {
            $this->defaultCountryCode = '51';
        }
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && strlen($digits) > 9) {
            $digits = ltrim($digits, '0');
        }

        if (strlen($digits) === 9) {
            return $this->defaultCountryCode.$digits;
        }

        return $digits;
    }

    public function buildMessageUrl(string $phone, string $message): string
    {
        $normalizedPhone = $this->normalizePhone($phone);

        return 'https://wa.me/'.$normalizedPhone.'?text='.rawurlencode($message);
    }

    protected function isBaileysMode(): bool
    {
        return $this->mode === 'baileys';
    }

    protected function resolveInstance(?string $instance): string
    {
        $name = trim((string) ($instance ?? ''));

        return $name !== '' ? $name : $this->defaultInstance;
    }

    protected function isApiConfigured(): bool
    {
        return $this->apiUrl !== '' && $this->apiKey !== '';
    }

    protected function requestApi(string $method, string $path, array $payload = []): array
    {
        if (! $this->isApiConfigured()) {
            return [
                'success' => false,
                'mode' => 'baileys',
                'error' => 'WhatsApp API no configurada. Define WHATSAPP_API_URL y WHATSAPP_API_KEY.',
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->withHeaders([
                    'X-API-KEY' => $this->apiKey,
                ])
                ->send($method, $this->apiUrl.$path, [
                    'json' => $payload,
                ]);

            $data = $response->json();
            if (is_array($data)) {
                return $data;
            }

            return [
                'success' => $response->successful(),
                'mode' => 'baileys',
                'status_code' => $response->status(),
                'raw' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::warning('Error consultando WhatsApp API', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'mode' => 'baileys',
                'error' => 'No se pudo conectar con el servicio de WhatsApp.',
                'detail' => $e->getMessage(),
            ];
        }
    }

    public function getQr(?string $instance = null): array
    {
        if ($this->isBaileysMode()) {
            $instanceName = $this->resolveInstance($instance);

            return $this->requestApi('GET', '/instance/'.rawurlencode($instanceName).'/qr');
        }

        return [
            'success' => true,
            'status' => 'browser_ready',
            'mode' => 'browser',
            'message' => 'Modo navegador activo. Los mensajes se abren directamente en WhatsApp.',
        ];
    }

    public function getStatus(?string $instance = null): array
    {
        if ($this->isBaileysMode()) {
            $instanceName = $this->resolveInstance($instance);

            return $this->requestApi('GET', '/instance/'.rawurlencode($instanceName).'/status');
        }

        return [
            'success' => true,
            'status' => 'browser_ready',
            'mode' => 'browser',
            'instance' => $instance,
        ];
    }

    public function sendMessage(string $session, string $phone, string $message): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === '') {
            return [
                'success' => false,
                'error' => 'Numero de WhatsApp invalido.',
            ];
        }

        if ($this->isBaileysMode()) {
            $instanceName = $this->resolveInstance($session);

            return $this->requestApi('POST', '/instance/'.rawurlencode($instanceName).'/send', [
                'phone' => $normalizedPhone,
                'message' => $message,
            ]);
        }

        return [
            'success' => true,
            'mode' => 'browser',
            'phone' => $normalizedPhone,
            'url' => $this->buildMessageUrl($normalizedPhone, $message),
            'message' => $message,
        ];
    }

    public function disconnect(?string $instance = null): array
    {
        if ($this->isBaileysMode()) {
            $instanceName = $this->resolveInstance($instance);

            return $this->requestApi('POST', '/instance/'.rawurlencode($instanceName).'/disconnect');
        }

        return [
            'success' => true,
            'status' => 'browser_ready',
            'mode' => 'browser',
            'message' => 'No hay una sesion persistente que desconectar en modo navegador.',
        ];
    }

    public function getChats(?string $instance = null): array
    {
        if ($this->isBaileysMode()) {
            $instanceName = $this->resolveInstance($instance);

            return $this->requestApi('GET', '/instance/'.rawurlencode($instanceName).'/chats');
        }

        return [
            'success' => true,
            'mode' => 'browser',
            'chats' => [],
            'message' => 'WhatsApp Web no permite listar chats desde el navegador sin una API dedicada.',
        ];
    }
}
