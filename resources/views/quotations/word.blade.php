<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <meta xmlns:w="urn:schemas-microsoft-com:office:word">
    <style>
        @page {
            mso-page-orientation: landscape;
            size: 29.7cm 21cm;
            margin: 1cm;
        }
        @page Section1 {
            size: 841.9pt 595.3pt;
            mso-page-orientation: landscape;
            margin: 1cm;
        }
        div.Section1 {
            page: Section1;
        }
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
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 12px;
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
            font-size: 12px;
            border: 1px solid #4a15bd;
        }
        td {
            padding: 9px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            color: #000;
            font-size: 13px;
        }
        .num {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-pending { color: #dc2626; font-weight: bold; }
        .status-in_process { color: #ca8a04; font-weight: bold; }
        .status-completed { color: #16a34a; font-weight: bold; }
        .client-company { font-weight: bold; display: block; }
        .client-name { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="Section1">
        <div class="header">
            <h1>{{ $reportTitle }}</h1>
            <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($selectedColumns as $column)
                        @if(isset($columnNames[$column]))
                            <th>{{ $columnNames[$column] }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($quotations as $quotation)
                <tr>
                    @foreach($selectedColumns as $column)
                    <td>
                        @switch($column)
                            @case('id') {{ $quotation->id }} @break
                            @case('empresa') {{ $quotation->client_company }} @break
                            @case('contacto') {{ $quotation->client_name }} @break
                            @case('email') {{ $quotation->client_email }} @break
                            @case('telefono') {{ $quotation->client_phone }} @break
                            @case('ruc') {{ $quotation->client_ruc }} @break
                            @case('direccion') {{ $quotation->client_address }} @break
                            @case('servicio') {{ $quotation->items->first()->service_name ?? '' }} @break
                            @case('descripcion') {{ $quotation->items->pluck('service_name')->implode(', ') }} @break
                            @case('total') <span class="num">S/ {{ number_format($quotation->total, 2) }}</span> @break
                            @case('fecha_envio') {{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }} @break
                            @case('fecha_respuesta') {{ $quotation->response_date ? \Carbon\Carbon::parse($quotation->response_date)->format('d/m/Y') : '-' }} @break
                            @case('mensaje') {{ $quotation->follow_up_message }} @break
                            @case('nota') {{ $quotation->follow_up_note }} @break
                            @case('usuario') {{ $quotation->user->name ?? 'N/A' }} @break
                            @case('estado') 
                                @switch($quotation->status)
                                    @case('pending') <span class="status-pending">Pendiente</span> @break
                                    @case('in_process') <span class="status-in_process">En Proceso</span> @break
                                    @case('completed') <span class="status-completed">Completado</span> @break
                                    @case('confirmed') <span class="status-completed">Confirmado</span> @break
                                    @default <span>{{ $quotation->status }}</span>
                                @endswitch
                                @break
                            @case('eliminado') {{ $quotation->deleted_at ? $quotation->deleted_at->format('Y-m-d H:i:s') : '' }} @break
                        @endswitch
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
