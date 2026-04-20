<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Cotización {{ $quotation->client_company ?? $quotation->client_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 0;
        }

        .page {
            width: 100%;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* Imágenes absolutas para header y footer */
        .header-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: 1;
        }

        .footer-img {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            padding: 70mm 20mm 50mm; /* Adjusted padding */
            box-sizing: border-box;
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

        /* Extra Pages */
        .page--extra {
            page-break-before: always;
            margin: 0;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .page--extra__image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
    </style>
</head>
<body>
    <div class="page">
        <img src="{{ public_path('images/cabezera.png') }}" class="header-img" alt="Header">
        <img src="{{ public_path('images/footer2.png') }}" class="footer-img" alt="Footer">

        <div class="content">
            <!-- Datos del Cliente -->
            <div class="client-section">
                <div class="section-title">Datos del Cliente</div>
                <div class="client-data">
                    <div class="client-row">
                        <span class="client-label">Empresa :</span>
                        <span class="client-value">{{ $quotation->client_company ?? $quotation->client_name }}</span>
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
                    <span class="total-value" style="font-size: 18px; color: #4b1c91;">S/ {{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Logic for Extra Pages based on Services --}}
    @php
        $selected_images = [];
        foreach($quotation->items as $item) {
            // Prioridad 1: Imagen personalizada seleccionada por el usuario (si existe en item)
            if(!empty($item->image_path)) {
                // Check if it's a full URL or relative path
                $imageName = basename($item->image_path); 
                // We need the full local path for PDF generation
                // If it's from storage (dynamic), path is storage/service_images/filename
                // If it's legacy (public/images), path is just filename
                
                // Let's assume item->image_path stores the URL. We need to find the file.
                // If the URL contains 'storage/service_images', it's a dynamic image.
                if(strpos($item->image_path, 'storage/service_images') !== false) {
                     $localPath = storage_path('app/public/service_images/' . $imageName);
                } else {
                     // Legacy fallback
                     $localPath = public_path('images/' . $imageName);
                }
                
                if(!in_array($localPath, $selected_images) && file_exists($localPath)) {
                    $selected_images[] = $localPath;
                }
            } 
            
            // Prioridad 2: Mapeo automático por nombre de servicio (BD)
            // Buscar mapeos para este servicio
            $mappings = \App\Models\ServiceMapping::where('service_name', $item->service_name)
                                                  ->with('serviceImage')
                                                  ->orderBy('order')
                                                  ->get();
                                                  
            foreach($mappings as $mapping) {
                if($mapping->serviceImage && $mapping->serviceImage->is_active) {
                    $localPath = storage_path('app/public/service_images/' . $mapping->serviceImage->filename);
                    if(!in_array($localPath, $selected_images) && file_exists($localPath)) {
                        $selected_images[] = $localPath;
                    }
                }
            }
            
            // Fallback: Si usaron un servicio antiguo que no está mapeado en BD, intentar buscar en legacy map (opcional, pero buena práctica)
            if(empty($mappings) && empty($item->image_path)) {
                $legacy_map = [
                    'WEB INFORMATIVA' => 'web-informativa.png',
                    'WEB E-COMMERCE' => 'E-COMERCE.png',
                    'POSICIONAMIENTO SEO' => 'posicionamiento-seo.png',
                    'WEB AULA VIRTUAL' => 'aula-virtual.png',
                    'PLUGIN YOAST SEO' => 'yoast-seo.png',
                    'RESTRUCTURACIÓN BÁSICA' => 'restructuracion.png',
                    'REDES SOCIALES' => 'REDES.png'
                ];
                $service = strtoupper(trim($item->service_name));
                if(isset($legacy_map[$service])) {
                    $localPath = public_path('images/' . $legacy_map[$service]);
                     if(!in_array($localPath, $selected_images) && file_exists($localPath)) {
                        $selected_images[] = $localPath;
                    }
                }
            }
        }
    @endphp

    @foreach($selected_images as $imagePath)
    <div class="page page--extra">
        <img class="page--extra__image" src="{{ $imagePath }}" alt="Detalle del servicio">
    </div>
    @endforeach
</body>
</html>
