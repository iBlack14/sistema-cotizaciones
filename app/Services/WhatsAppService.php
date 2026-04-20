<?php

namespace App\Services;

class WhatsAppService
{
    protected string $defaultCountryCode;

    public function __construct()
    {
        $this->defaultCountryCode = preg_replace('/\D+/', '', (string) config('app.whatsapp_country_code', env('WHATSAPP_COUNTRY_CODE', '51')));

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
            return $this->defaultCountryCode . $digits;
        }

        return $digits;
    }

    public function buildMessageUrl(string $phone, string $message): string
    {
        $normalizedPhone = $this->normalizePhone($phone);

        return 'https://wa.me/' . $normalizedPhone . '?text=' . rawurlencode($message);
    }

    public function getQr(?string $instance = null): array
    {
        return [
            'success' => true,
            'status' => 'browser_ready',
            'mode' => 'browser',
            'message' => 'Modo navegador activo. Los mensajes se abren directamente en WhatsApp.',
        ];
    }

    public function getStatus(?string $instance = null): array
    {
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
        return [
            'success' => true,
            'status' => 'browser_ready',
            'mode' => 'browser',
            'message' => 'No hay una sesion persistente que desconectar en modo navegador.',
        ];
    }

    public function getChats(?string $instance = null): array
    {
        return [
            'success' => true,
            'mode' => 'browser',
            'chats' => [],
            'message' => 'WhatsApp Web no permite listar chats desde el navegador sin una API dedicada.',
        ];
    }
}
