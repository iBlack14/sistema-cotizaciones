<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Modern Reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151; /* Gray 700 */
            background-color: #f3f4f6; /* Gray 100 */
        }
        
        /* Container styling for Card effect */
        .email-wrapper {
            background-color: #f3f4f6;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Premium Header */
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        
        /* Content Area */
        .content {
            padding: 40px 35px;
            background-color: #ffffff;
        }

        .message-content {
            font-size: 16px;
            color: #4b5563; /* Gray 600 */
            line-height: 1.8;
        }

        /* Call to Action Section */
        .cta-container {
            margin-top: 35px;
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(to right, #7c3aed, #6d28d9);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px; /* Pill shape */
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px -1px rgba(124, 58, 237, 0.4);
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(124, 58, 237, 0.5);
        }
        
        /* Footer styling */
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }

        .footer a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }

        /* Typography improvements */
        h1, h2, h3 { color: #1f2937; margin-top: 0; }
        p { margin-bottom: 1.5em; }
        
        /* Utility */
        .text-center { text-align: center; }
        .text-sm { font-size: 14px; }
        .text-muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="header">
                <h1>{{ config('app.name', 'Sistema de Dominios') }}</h1>
            </div>
            
            <!-- Content -->
            <div class="content">
                <div class="message-content">
                    {!! nl2br(e($content)) !!}
                </div>
                
                <div class="cta-container">
                    <p class="text-sm text-muted" style="margin-bottom: 20px;">
                        <strong>¿Tienes alguna duda?</strong><br>
                        Responde a este correo y te ayudaremos.
                    </p>
                    <a href="{{ config('app.url') }}" class="btn">Acceder al Portal</a>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p style="margin-bottom: 10px;">
                    © {{ date('Y') }} <strong>{{ config('app.name', 'Sistema de Dominios') }}</strong>.<br>
                    Todos los derechos reservados.
                </p>
                <div style="font-size: 11px; line-height: 1.4;">
                    Estás recibiendo este correo porque tienes servicios activos con nosotros.<br>
                    Si crees que esto es un error, puedes <a href="#">actualizar tus preferencias</a>.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
