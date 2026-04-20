<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta xmlns:w="urn:schemas-microsoft-com:office:word">
    <style>
        @page {
            mso-page-orientation: landscape;
            size: 29.7cm 21cm;
            margin: 1.5cm;
        }
        @page Section1 {
            size: 841.9pt 595.3pt;
            mso-page-orientation: landscape;
            margin: 1.5cm;
        }
        div.Section1 { page: Section1; }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #5F1BF2;
            font-size: 26px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #5F1BF2;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #4a15bd;
        }
        td {
            padding: 9px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            color: #000;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .num {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .status-activo { color: #16a34a; font-weight: bold; }
        .status-expirado { color: #dc2626; font-weight: bold; }
        .status-por_vencer { color: #ca8a04; font-weight: bold; }
    </style>
</head>
<body>
    <div class="Section1">
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Generado el: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Total: {{ $domains->count() }} dominios</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Dominio</th>
                    <th>Cliente / Empresa</th>
                    <th>Teléfono</th>
                    <th>Correos</th>
                    <th>Precio</th>
                    <th>F. Activación</th>
                    <th>F. Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($domains as $i => $domain)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $domain->domain_name }}</strong></td>
                    <td>{{ $domain->client_name ?? '-' }}</td>
                    <td class="num">{{ $domain->phone ?? '-' }}</td>
                    <td style="font-size:12px;">
                        {{ $domain->corporate_emails ?? '' }}
                        @if($domain->emails)
                            {{ $domain->corporate_emails ? ' | ' : '' }}{{ $domain->emails }}
                        @endif
                        @if(!$domain->corporate_emails && !$domain->emails) - @endif
                    </td>
                    <td class="num">{{ $domain->price ? 'S/ ' . number_format($domain->price, 2) : '-' }}</td>
                    <td>{{ $domain->activation_date ? \Carbon\Carbon::parse($domain->activation_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $domain->expiration_date ? \Carbon\Carbon::parse($domain->expiration_date)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @php
                            $status = $domain->status ?? 'activo';
                        @endphp
                        <span class="status-{{ $status }}">
                            {{ $status === 'activo' ? 'Activo' : ($status === 'expirado' ? 'Expirado' : 'Por Vencer') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
