<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $subject ?? 'Renovación de Servicio' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f0f2f5; -webkit-font-smoothing: antialiased;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f0f2f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 650px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
                    
                    <!-- Header with Vibrant Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6b2fff 0%, #4b1fff 100%); padding: 50px 40px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        @if(isset($logoData))
                                            <img src="{{ $message->embedData($logoData, 'logo.png', 'image/png') }}" alt="Logo" style="height: 60px; width: auto; margin-bottom: 20px;" />
                                        @else
                                            <div style="font-size: 32px; font-weight: 800; color: #ffffff; letter-spacing: -1px; margin-bottom: 20px;">VÍA COMUNICATIVA</div>
                                        @endif
                                        <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); padding: 8px 16px; border-radius: 30px; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                            Aviso de renovación
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 50px;">
                            <h1 style="margin: 0 0 15px; font-size: 28px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px;">
                                Hola, {{ $clientName ?? 'Cliente' }}
                            </h1>
                            <p style="margin: 0 0 30px; font-size: 17px; line-height: 1.6; color: #4b5563;">
                                Tu presencia digital es importante para nosotros. Queremos recordarte que uno de tus servicios está próximo a vencer.
                            </p>

                            <!-- Alert Panel (Glassmorphism inspired) -->
                            @php
                                $statusColor = '#f59e0b'; // Default orange
                                $statusBg = '#fff7ed';
                                $statusIcon = '⏰';
                                $statusTitle = 'Recordatorio de Renovación';
                                
                                if(isset($daysUntilExpiration) && $daysUntilExpiration <= 0) {
                                    $statusColor = '#ef4444'; 
                                    $statusBg = '#fef2f2';
                                    $statusIcon = '⚠️';
                                    $statusTitle = 'Servicio Expirado';
                                } elseif(isset($daysUntilExpiration) && $daysUntilExpiration <= 7) {
                                    $statusColor = '#ef4444';
                                    $statusBg = '#fef2f2';
                                    $statusIcon = '🚨';
                                    $statusTitle = 'Renovación Urgente';
                                }
                            @endphp

                            <div style="background-color: {{ $statusBg }}; border-left: 5px solid {{ $statusColor }}; padding: 25px; border-radius: 16px; margin-bottom: 35px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="font-size: 24px; width: 45px; vertical-align: top;">{{ $statusIcon }}</td>
                                        <td>
                                            <strong style="display: block; font-size: 18px; color: {{ $statusColor }}; margin-bottom: 4px;">{{ $statusTitle }}</strong>
                                            <p style="margin: 0; font-size: 15px; color: #374151; line-height: 1.5;">
                                                Dominio: <strong>{{ $domainName ?? 'no disponible' }}</strong><br>
                                                Expira en: <strong>{{ $daysUntilExpiration ?? '30' }} días</strong> ({{ $expirationDate ?? 'Pronto' }})
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Pricing Section -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 35px; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden;">
                                <tr style="background-color: #f9fafb;">
                                    <td style="padding: 15px 20px; font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Servicio</td>
                                    <td style="padding: 15px 20px; font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Costo</td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px; font-size: 16px; color: #111827; font-weight: 500;">Renovación de dominio {{ $domainName }}</td>
                                    <td style="padding: 20px; font-size: 18px; font-weight: 700; color: #111827; text-align: right;">S/ {{ number_format($price ?? 0, 2) }}</td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td align="right" style="padding: 20px; font-size: 18px; font-weight: 600; color: #4b5563;">Total a pagar:</td>
                                    <td style="padding: 20px; font-size: 24px; font-weight: 800; color: #4f46e5; text-align: right;">S/ {{ number_format($totalPrice ?? $price ?? 0, 2) }}</td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="https://{{ $website ?? 'viacomunicativa.com' }}/pagar" style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; padding: 18px 45px; border-radius: 40px; font-size: 17px; font-weight: 700; text-decoration: none; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);">
                                            Renovar Servicio Ahora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 40px 0 0; font-size: 14px; color: #9ca3af; text-align: center; line-height: 1.5;">
                                Si tienes dudas sobre tu facturación, puedes responder este correo o contactarnos vía WhatsApp.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 40px; border-top: 1px solid #f3f4f6; text-align: center;">
                            <div style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                                <strong>Vía Comunicativa</strong><br>
                                {{ $address ?? 'Chiclayo - Lima - Trujillo' }}<br>
                                Teléfono: {{ $phone ?? '936 613 758' }}
                            </div>
                            <table align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 0 10px;">
                                        <a href="https://viacomunicativa.com" style="color: #4f46e5; text-decoration: none; font-size: 13px; font-weight: 600;">Sitio Web</a>
                                    </td>
                                    <td style="color: #d1d5db;">|</td>
                                    <td style="padding: 0 10px;">
                                        <a href="https://wa.me/51936613758" style="color: #4f46e5; text-decoration: none; font-size: 13px; font-weight: 600;">Soporte WhatsApp</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <p style="margin-top: 30px; font-size: 12px; color: #9ca3af; text-align: center;">
                    &copy; {{ date('Y') }} Vía Comunicativa. Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
