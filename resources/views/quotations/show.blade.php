<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Cotización {{ $quotation->client_company ?? $quotation->client_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            max-height: 100vh;
            overflow-y: auto;
        }
        .preview-actions {
            position: sticky;
            top: 0;
            display: flex;
            justify-content: flex-start;
            margin: 0 auto 12px;
            max-width: 210mm;
            z-index: 5;
        }
        #vc-download-pdf {
            background: #4b1c91;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        #vc-download-pdf:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        #vc-send-email {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-left: 10px;
        }
        #vc-send-whatsapp {
            background: #25D366;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-left: 10px;
        }
        #vc-send-whatsapp:hover {
            background: #128C7E;
        }
        #vc-send-email-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-left: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        #vc-send-email-whatsapp:hover {
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        #vc-send-email-whatsapp:active {
            transform: translateY(0);
        }
        #vc-send-email-whatsapp:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        #vc-send-email-whatsapp::before {
            content: '📧';
            margin-right: 6px;
        }
        /* Botón Volver */
        .btn-back {
            background: #666;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back:hover {
            background: #555;
        }
        .page {
            width: 210mm;
            /* Ancho A4 */
            min-height: 297mm;
            /* Alto A4 */
            margin: 0 auto;
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 0;
        }
        /* Imagen de cabecera */
        .header-decoration {
            position: absolute;
            top: 0;
            /* Sin margen superior */
            left: 0;
            right: 0;
            height: 70mm;
            /* Aumentado para que ocupe más espacio */
            background: url("{{ asset('images/cabezera.png') }}") no-repeat center top;
            background-size: 100% auto;
            z-index: 1;
        }
        .header-date {
            /* Ajuste solicitado: un poco más abajo y hacia la izquierda */
            position: absolute;
            top: 30mm;
            right: 80mm;
            font-size: 14px;
            font-weight: bold;
            color: #4b1c91;
            background: rgba(255, 255, 255, 0.9);
            padding: 6px 18px;
            border-radius: 4px;
            letter-spacing: 1px;
        }
        /* Imagen de pie de página */
        .footer-decoration {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: auto;
            min-height: 80mm;
            background: url("{{ asset('images/footer2.png') }}") no-repeat center bottom;
            background-size: contain;
            background-position-y: bottom;
            z-index: 1;
            aspect-ratio: 1/1.5;
        }
        .content {
            position: relative;
            z-index: 2;
            padding: 70mm 20mm 90mm;
            min-height: 297mm;
            box-sizing: border-box;
        }
        /* Logo y encabezado */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 48px;
            font-weight: bold;
            color: #6e1a94;
        }
        .tagline {
            font-size: 11px;
            letter-spacing: 2px;
            color: #999;
            text-transform: uppercase;
        }
        .company-name {
            text-align: right;
            font-size: 11px;
            letter-spacing: 2px;
            color: #999;
            text-transform: uppercase;
        }
        /* Sección datos del cliente */
        .client-section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            color: #333;
        }
        .client-data {
            font-size: 13px;
            line-height: 1.8;
            color: #333;
        }
        .client-row {
            margin-bottom: 5px;
        }
        .client-label {
            display: inline-block;
            width: 80px;
            font-weight: normal;
        }
        .client-value {
            display: inline;
            font-weight: normal;
        }
        .fecha-row {
            margin-top: 15px;
            font-size: 13px;
            font-weight: bold;
        }
        .vc-extra-pages {
            margin-top: 20px;
        }
        .page--extra {
            margin: 20px auto 0;
            display: block;
            width: 210mm;
            min-height: 297mm;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background: #fff;
        }
        .page--extra__image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .page--extra__date {
            position: absolute;
            top: 58mm;
            right: 80mm;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: bold;
            color: #292927;
            font-size: 14px;
            letter-spacing: 1px;
        }
        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        thead {
            background: #333;
            color: white;
        }
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: capitalize;
            border: 1px solid #333;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        tbody tr {
            background: #f9f9f9;
        }
        .amount {
            text-align: right;
        }
        /* Total */
        .total-section {
            text-align: right;
            margin: 20px 0;
            font-size: 14px;
            font-weight: bold;
            padding-right: 10px;
        }
        .total-label {
            display: inline-block;
            margin-right: 20px;
        }
        .total-value {
            display: inline-block;
            font-size: 16px;
            color: #333;
        }
        /* Notas */
        .notes {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
            margin: 20px 0;
        }
        /* Footer */
        .footer-info {
            display: none;
            /* Ocultamos el footer de texto ya que usamos imagen */
        }
        .signature {
            text-align: right;
            margin-top: 40px;
            font-weight: bold;
            color: #333;
            font-size: 12px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                max-width: 100%;
            }
            .preview-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    @if(request('clean'))
        <style>
            body {
                padding: 0 !important;
                background-color: #ffffff !important;
                overflow-y: hidden;
                /* Disable vertical scrolling */
                zoom: 0.6;
                /* Use zoom for better layout scaling */
            }
            .page {
                margin: 0 auto !important;
                box-shadow: none !important;
            }
        </style>
    @endif
    @unless(request('clean'))
        <div class="preview-actions">
            <a href="{{ route('quotations.create') }}" class="btn-back">← Volver</a>
            <button id="vc-download-pdf" type="button">Descargar PDF</button>
            <button id="vc-send-email" type="button">Enviar por Email</button>
            <button id="vc-send-whatsapp" type="button">Enviar por WhatsApp</button>
            <button id="vc-send-email-whatsapp" type="button">Email + WhatsApp</button>
        </div>
    @endunless
    <div id="vc-preview-document">
        <div class="page">
            <div class="header-decoration"></div>
            <div class="header-date">{{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }}</div>
            <div class="footer-decoration"></div>
            <div class="content">
                <!-- Datos del Cliente -->
                <div class="client-section">
                    <div class="section-title">Datos del Cliente</div>
                    <div class="client-data">
                        <div class="client-row">
                            <span class="client-label">Empresa :</span>
                            <span
                                class="client-value">{{ $quotation->client_company ?? $quotation->client_name }}</span>
                        </div>
                        @if($quotation->client_ruc)
                            <div class="client-row">
                                <span class="client-label">RUC :</span>
                                <span class="client-value">{{ $quotation->client_ruc }}</span>
                            </div>
                        @endif
                        @if($quotation->client_phone)
                            <div class="client-row">
                                <span class="client-label">Teléfono :</span>
                                <span class="client-value">{{ $quotation->client_phone }}</span>
                            </div>
                        @endif
                        @if($quotation->client_email)
                            <div class="client-row">
                                <span class="client-label">Correo :</span>
                                <span class="client-value">{{ $quotation->client_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Fecha -->
                <div class="fecha-row">
                    Fecha: {{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }}
                </div>
                <!-- Tabla de Items -->
                <table>
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th class="amount">Precio</th>
                            <th class="amount">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $item)
                            <tr>
                                <td>{{ $item->service_name }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td class="amount">S/ {{ number_format($item->price, 2) }}</td>
                                <td class="amount">S/ {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Total -->
                <div class="total-section">
                    <div style="margin-bottom: 5px;">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-value">S/ {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    @if($quotation->igv > 0)
                        <div style="margin-bottom: 5px;">
                            <span class="total-label">IGV (18%):</span>
                            <span class="total-value">S/ {{ number_format($quotation->igv, 2) }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="total-label" style="font-size: 18px;">Total:</span>
                        <span class="total-value"
                            style="font-size: 18px; color: #4b1c91;">S/ {{ number_format($quotation->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Logic for Extra Pages based on Services --}}
        {{-- Logic for Extra Pages based on Services --}}
        @php
            $selected_pages = [];
            // 1. Obtener mapeos de la base de datos para los servicios actuales
            $serviceNames = $quotation->items->pluck('service_name')->toArray();
            $mappings = \App\Models\ServiceMapping::with('serviceImage')
                ->whereIn('service_name', $serviceNames)
                ->orderBy('order')
                ->get();
            // Agrupar mapeos por servicio
            $db_map = [];
            foreach ($mappings as $map) {
                if ($map->serviceImage) {
                    $db_map[$map->service_name][] = $map->serviceImage;
                }
            }
            // 2. Mapeo Legacy (para servicios antiguos o imágenes estáticas)
            // Solo se usa si NO hay mapeo en base de datos para ese servicio
            $legacy_map = [
                'WEB INFORMATIVA' => 'web-informativa.png',
                'WEB E-COMMERCE' => 'E-COMERCE.png',
                'WEB FUSION E-COMMERCE' => ['web-informativa.png', 'E-COMERCE.png'],
                'POSICIONAMIENTO SEO' => 'posicionamiento-seo.png',
                'WEB AULA VIRTUAL' => 'aula-virtual.png',
                'WEB FUSION AULA VIRTUAL' => ['web-informativa.png', 'aula-virtual.png'],
                'PLUGIN YOAST SEO' => 'yoast-seo.png',
                'RESTRUCTURACIÓN BÁSICA' => 'restructuracion.png',
                'REDES SOCIALES' => 'REDES.png'
            ];
            foreach ($quotation->items as $item) {
                // Caso A: Imagen Personalizada (subida al crear item)
                if (!empty($item->image_path)) {
                    $selected_pages[] = [
                        'url' => url('storage/' . $item->image_path),
                        'type' => 'custom'
                    ];
                    continue;
                }
                $serviceName = $item->service_name;
                // Caso B: Mapeo desde Base de Datos (Prioridad)
                $foundInDb = false;
                if (isset($db_map[$serviceName])) {
                    foreach ($db_map[$serviceName] as $imgModel) {
                        // Validar existencia física del archivo para evitar imágenes rotas
                        $filePath = public_path('storage/' . $imgModel->path);
                        if (file_exists($filePath)) {
                            $selected_pages[] = [
                                'url' => url('storage/' . $imgModel->path),
                                'type' => 'db'
                            ];
                            $foundInDb = true;
                        }
                    }
                }
                // Si se encontró en DB y existen los archivos, no buscamos en legacy
                if ($foundInDb)
                    continue;
                // Caso C: Fallback Legacy (Imágenes estáticas en public/images)
                // Normalizar nombre para búsqueda (mayúsculas)
                $upperService = strtoupper(trim($serviceName));
                // Mapeo directo
                if (isset($legacy_map[$upperService])) {
                    $imgs = (array) $legacy_map[$upperService];
                    foreach ($imgs as $imgName) {
                        $selected_pages[] = [
                            'url' => asset('images/' . $imgName),
                            'type' => 'static'
                        ];
                    }
                }
                // Fallback específico para Redes si falla el exacto
                else if (str_contains($upperService, 'REDES')) {
                    $selected_pages[] = [
                        'url' => asset('images/REDES.png'),
                        'type' => 'static_fallback'
                    ];
                }
            }
            // Eliminar duplicados de URL
            $unique_pages = [];
            $seen_urls = [];
            foreach ($selected_pages as $page) {
                if (!in_array($page['url'], $seen_urls)) {
                    $seen_urls[] = $page['url'];
                    $unique_pages[] = $page;
                }
            }
        @endphp
        @foreach($unique_pages as $page)
            <div class="page page--extra">
                <img class="page--extra__image" src="{{ $page['url'] }}" alt="Detalle del servicio">
                <div class="page--extra__date">{{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }}</div>
            </div>
        @endforeach
        <div class="vc-extra-pages"></div>
    </div>
    <script>
        // If ?public=1 is present, hide action buttons and interactive controls
        (function () {
            try {
                var params = new URLSearchParams(window.location.search);
                if (params.get('public') === '1' || params.get('public') === 'true') {
                    var actions = document.querySelectorAll('.preview-actions, #vc-download-pdf, #vc-send-email, #vc-send-whatsapp');
                    actions.forEach(function (el) { if (el && el.style) el.style.display = 'none'; });
                }
            } catch (e) {
                // ignore
            }
        })();
    </script>
    <!-- Email Modal -->
    <div id="email-modal" class="vc-modal">
        <div class="vc-modal-content">
            <div class="vc-modal-header">
                <h3>Enviar Cotización</h3>
                <button type="button" id="close-email-modal" class="vc-close-btn">&times;</button>
            </div>
            <div class="vc-modal-body">
                <form id="email-form">
                    <div class="vc-input-group">
                        <label for="email-address">Correo electrónico del cliente</label>
                        <input type="email" id="email-address" name="email" placeholder="ejemplo@empresa.com" required
                            value="{{ $quotation->client_email }}">
                    </div>
                    <p class="vc-modal-note">
                        <span class="vc-icon-info">ℹ️</span> El PDF se generará y enviará automáticamente.
                    </p>
                    <div class="vc-modal-actions">
                        <button type="button" id="cancel-email-btn" class="vc-btn-secondary">Cancelar</button>
                        <button type="submit" class="vc-btn-primary">
                            <span class="btn-text">Enviar PDF Ahora</span>
                            <span class="btn-loader" style="display:none"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        /* Modal Styles */
        .vc-modal {
            display: none;
            position: fixed;
            z-index: 100000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        .vc-modal-content {
            background-color: #fff;
            margin: 10vh auto;
            width: 450px;
            max-width: 90%;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .vc-modal-header {
            background: linear-gradient(135deg, #b207b6 0%, #e91e63 100%);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .vc-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .vc-close-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            transition: color 0.2s;
        }
        .vc-close-btn:hover {
            color: white;
        }
        .vc-modal-body {
            padding: 24px;
        }
        .vc-input-group {
            margin-bottom: 20px;
        }
        .vc-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }
        .vc-input-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        .vc-input-group input:focus {
            outline: none;
            border-color: #b207b6;
            box-shadow: 0 0 0 3px rgba(178, 7, 182, 0.1);
        }
        .vc-modal-note {
            background-color: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .vc-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .vc-btn-secondary {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .vc-btn-secondary:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }
        .vc-btn-primary {
            background: linear-gradient(135deg, #b207b6 0%, #e91e63 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(178, 7, 182, 0.2);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .vc-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(178, 7, 182, 0.3);
        }
        .vc-btn-primary:active {
            transform: translateY(0);
        }
        .vc-btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        /* Spinner */
        .vc-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            display: none;
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        /* Success Modal */
        .vc-success-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10001;
            animation: fadeIn 0.3s ease-out;
        }
        .vc-success-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideDown 0.3s ease-out;
        }
        .vc-success-icon {
            width: 60px;
            height: 60px;
            background-color: #d1fae5;
            color: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .vc-success-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
        }
        .vc-success-message {
            color: #6b7280;
            margin-bottom: 24px;
        }
    </style>
    <!-- Success Modal HTML -->
    <div id="vc-success-modal" class="vc-success-modal">
        <div class="vc-success-content">
            <div class="vc-success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="vc-success-title">¡Envío Exitoso!</h3>
            <p class="vc-success-message">La cotización ha sido enviada correctamente al cliente.</p>
            <button onclick="document.getElementById('vc-success-modal').style.display='none'" class="vc-btn-primary"
                style="width: 100%; justify-content: center;">
                Aceptar
            </button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        (function () {
            function libsReady() {
                return typeof window.html2canvas !== 'undefined' && (window.jspdf || window.jsPDF);
            }
            function attachHandler() {
                const downloadBtn = document.getElementById('vc-download-pdf');
                const element = document.getElementById('vc-preview-document');
                if (!downloadBtn || !element) {
                    return false;
                }
                if (!libsReady()) {
                    return false;
                }
                // Helper to generate PDF Blob
                function getPDFBlob() {
                    return new Promise((resolve, reject) => {
                        const jsPdfNamespace = window.jspdf || window.jsPDF;
                        if (!libsReady()) {
                            reject('Librerías no cargadas');
                            return;
                        }
                        const element = document.getElementById('vc-preview-document');
                        const visiblePages = Array.from(element.querySelectorAll('.page'))
                            .filter(function (page) {
                                return window.getComputedStyle(page).display !== 'none';
                            });
                        if (visiblePages.length === 0) {
                            reject('No hay páginas para exportar');
                            return;
                        }
                        const jsPDFConstructor = jsPdfNamespace.jsPDF || jsPdfNamespace;
                        const pdf = new jsPDFConstructor('p', 'mm', 'a4');
                        const pageWidth = 210;
                        const pageHeight = 297;
                        function renderPage(index) {
                            if (index >= visiblePages.length) {
                                resolve(pdf.output('blob'));
                                return;
                            }
                            const pageNode = visiblePages[index];
                            window.html2canvas(pageNode, {
                                scale: 2,
                                useCORS: true,
                                scrollY: -window.scrollY,
                                backgroundColor: '#ffffff'
                            }).then(function (canvas) {
                                const imgData = canvas.toDataURL('image/jpeg', 0.98);
                                pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pageHeight, undefined, 'FAST');
                                if (index < visiblePages.length - 1) {
                                    pdf.addPage('a4', 'portrait');
                                }
                                renderPage(index + 1);
                            }).catch(reject);
                        }
                        renderPage(0);
                    });
                }
                // Expose getPDFBlob globally so email handler can use it
                window.vcGetPDFBlob = getPDFBlob;
                downloadBtn.addEventListener('click', function () {
                    downloadBtn.disabled = true;
                    const originalText = downloadBtn.textContent;
                    downloadBtn.textContent = 'Generando...';
                    getPDFBlob().then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'COT-{{ strtoupper(\Illuminate\Support\Str::slug($quotation->client_company ?? $quotation->client_name)) }}-{{ $quotation->date }}.pdf';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    }).catch(err => {
                        console.error(err);
                        alert('Error al generar PDF');
                    }).finally(() => {
                        downloadBtn.disabled = false;
                        downloadBtn.textContent = originalText;
                    });
                });
                return true;
            }
            // Inject Settings
            window.vcSettings = <?php echo json_encode($settings); ?>;
            // Shared WhatsApp function
            function openWhatsApp() {
                let phoneNumber = '{{ $quotation->client_phone }}';
                let companyName = '{{ $quotation->client_company ?? $quotation->client_name }}';
                phoneNumber = phoneNumber.replace(/[\s\-\(\)]/g, '');
                if (!phoneNumber) {
                    alert('No se encontró un número de teléfono válido.');
                    return;
                }
                if (phoneNumber.startsWith('+51')) {
                    phoneNumber = phoneNumber.substring(3);
                }
                if (!phoneNumber.startsWith('51') && phoneNumber.length === 9) {
                    phoneNumber = '51' + phoneNumber;
                }
                // Get Template (Default to Initial)
                let template = window.vcSettings['quotation_whatsapp_message'] || "Hola [Nombre], te adjunto la cotización para [Servicio]. Quedo atento.";
                // Replacements
                let messageText = template
                    .replace(/\[Nombre\]/g, '{{ $quotation->client_name ?? $quotation->client_company }}')
                    .replace(/\[Empresa\]/g, '{{ $quotation->client_company ?? "" }}')
                    .replace(/\[RUC\]/g, '{{ $quotation->client_ruc ?? "" }}')
                    .replace(/\[Fecha\]/g, '{{ \Carbon\Carbon::parse($quotation->date)->format("d/m/Y") }}')
                    .replace(/\[Servicio\]/g, '{{ $quotation->items->first()->service_name ?? "" }}')
                    .replace(/\[Total\]/g, '{{ number_format($quotation->total, 2) }}')
                    .replace(/\[Link\]/g, '{{ $quotation->slug ? route("quotations.public", $quotation->slug) : \Illuminate\Support\Facades\URL::signedRoute("quotations.download", $quotation) }}');
                const encodedMessage = encodeURIComponent(messageText);
                const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
                window.open(whatsappUrl, '_blank');
            }
            // WhatsApp functionality
            function attachWhatsAppHandler() {
                const sendWhatsAppBtn = document.getElementById('vc-send-whatsapp');
                if (sendWhatsAppBtn) {
                    sendWhatsAppBtn.addEventListener('click', openWhatsApp);
                }
                return true;
            }
            // Email modal functionality
            function attachEmailHandler() {
                const sendEmailBtn = document.getElementById('vc-send-email');
                const emailModal = document.getElementById('email-modal');
                const emailForm = document.getElementById('email-form');
                const emailInput = document.getElementById('email-address');
                const closeBtn = document.getElementById('close-email-modal');
                const cancelBtn = document.getElementById('cancel-email-btn');
                if (!sendEmailBtn || !emailModal || !emailForm) {
                    return false;
                }
                function closeModal() {
                    emailModal.style.display = 'none';
                }
                // Open modal
                sendEmailBtn.addEventListener('click', function () {
                    emailModal.style.display = 'block';
                    setTimeout(() => emailInput.focus(), 100);
                });
                // Close modal
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
                // Close modal when clicking outside
                emailModal.addEventListener('click', function (e) {
                    if (e.target === emailModal) {
                        closeModal();
                    }
                });
                // Shared function to send email
                function performSendEmail(email, btnElement) {
                    console.log('Initiating performSendEmail for:', email);
                    const originalContent = btnElement.innerHTML;
                    const spinner = document.createElement('div');
                    spinner.className = 'vc-spinner';
                    // Show loading state
                    btnElement.disabled = true;
                    btnElement.innerHTML = '';
                    btnElement.appendChild(spinner);
                    spinner.style.display = 'inline-block';
                    btnElement.appendChild(document.createTextNode(' Enviando...'));
                    if (!window.vcGetPDFBlob) {
                        alert('Error: Función de generación de PDF no disponible. Recarga la página.');
                        btnElement.innerHTML = originalContent;
                        btnElement.disabled = false;
                        return;
                    }
                    console.log('Generating PDF Blob...');
                    window.vcGetPDFBlob().then(blob => {
                        console.log('PDF Blob generated, size:', blob.size);
                        const formData = new FormData();
                        formData.append('email', email);
                        formData.append('pdf_file', blob, 'Cotizacion-{{ $quotation->id }}.pdf');
                        formData.append('_token', '{{ csrf_token() }}');
                        console.log('Sending fetch request...');
                        fetch("{{ route('quotations.send-email', $quotation) }}", {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                            .then(response => {
                                console.log('Fetch response status:', response.status);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Fetch response data:', data);
                                if (data.success) {
                                    if (document.getElementById('email-modal').style.display === 'block') {
                                        closeModal();
                                    }
                                    document.getElementById('vc-success-modal').style.display = 'block';
                                } else {
                                    alert('Error al enviar PDF: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Fetch Error:', error);
                                alert('Error de red al enviar el PDF. Revisa la consola.');
                            })
                            .finally(() => {
                                btnElement.innerHTML = originalContent;
                                btnElement.disabled = false;
                            });
                    })
                        .catch(error => {
                            console.error('PDF Generation Error:', error);
                            alert('Error al generar el PDF: ' + error);
                            btnElement.innerHTML = originalContent;
                            btnElement.disabled = false;
                        });
                }
                // Email + WhatsApp functionality
                const sendEmailWhatsAppBtn = document.getElementById('vc-send-email-whatsapp');
                if (sendEmailWhatsAppBtn) {
                    sendEmailWhatsAppBtn.addEventListener('click', function () {
                        const clientEmail = '{{ $quotation->client_email }}';
                        // 1. Open WhatsApp immediately (preserves user gesture)
                        openWhatsApp();
                        // 2. Send Email
                        if (clientEmail) {
                            performSendEmail(clientEmail, sendEmailWhatsAppBtn);
                        } else {
                            // Fallback to modal if no email
                            emailModal.style.display = 'block';
                            setTimeout(() => emailInput.focus(), 100);
                        }
                    });
                }
                // Handle form submission (Manual Email Modal)
                emailForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const email = emailInput.value;
                    if (!email) {
                        alert('Por favor ingresa un email válido.');
                        return;
                    }
                    const submitBtn = emailForm.querySelector('button[type="submit"]');
                    performSendEmail(email, submitBtn);
                });
                return true;
            }
            function waitForAssets(retries = 0) {
                if ((!attachHandler() || !attachEmailHandler() || !attachWhatsAppHandler()) && retries < 200) {
                    setTimeout(function () {
                        waitForAssets(retries + 1);
                    }, 150);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    waitForAssets();
                });
            } else {
                waitForAssets();
            }
        })();
    </script>
</body>
</html>
