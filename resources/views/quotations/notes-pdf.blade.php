<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111;
            font-size: 10pt;
        }

        .page {
            position: relative;
            width: 210mm;
            height: 297mm;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .page-month {
            position: absolute;
            top: 10mm;
            left: 0;
            width: 210mm;
            text-align: center;
            font-size: 18pt;
            font-weight: 700;
            color: #111;
            letter-spacing: 0.4mm;
            text-transform: uppercase;
            z-index: 3;
        }

        .bg {
            position: absolute;
            left: 0;
            top: 0;
            width: 210mm;
            height: 297mm;
            z-index: 0;
        }

        .note-slot {
            position: absolute;
            width: 72.8mm;
            height: 107.0mm;
            z-index: 2;
        }

        .slot-1 { left: 17.2mm; top: 46.5mm; }
        .slot-2 { left: 113.6mm; top: 46.5mm; }
        .slot-3 { left: 17.2mm; top: 168.1mm; }
        .slot-4 { left: 113.6mm; top: 168.1mm; }

        .value {
            position: absolute;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            line-height: 1.15;
        }

        .fecha {
            position: absolute;
            left: 0;
            top: 7.0mm;
            width: 72.8mm;
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .empresa  { left: 17.5mm; top: 22.8mm; width: 53.5mm; }
        .ruc      { left: 7.0mm;  top: 32.5mm; width: 64.0mm; }
        .telefono { left: 7.0mm;  top: 43.1mm; width: 22.0mm; }
        .servicio { left: 31.0mm; top: 43.1mm; width: 40.0mm; }
        .telefono,
        .servicio {
            font-size: 8pt;
        }

        .descripcion {
            position: absolute;
            left: 7.0mm;
            top: 49.8mm;
            width: 64.0mm;
            height: 33.0mm;
            overflow: hidden;
            line-height: 1.2;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    @php($bgFile = public_path('images/notas.jpg'))
    @php($bgData = is_file($bgFile) ? base64_encode(file_get_contents($bgFile)) : null)
    @php($bgSrc = $bgData ? ('data:image/jpeg;base64,' . $bgData) : '')
    @php($chunks = collect($notes)->chunk(4))

    @foreach ($chunks as $chunk)
        <div class="page">
            <div class="page-month">{{ $exportMonthTitle ?? '' }}</div>
            @if ($bgSrc !== '')
                <img class="bg" src="{{ $bgSrc }}" alt="Plantilla Notas">
            @endif
            @for ($i = 0; $i < 4; $i++)
                @php($note = $chunk->get($i))
                @if ($note)
                    <div class="note-slot slot-{{ $i + 1 }}">
                        <div class="fecha">{{ mb_strtoupper($note['llamar'] ?: now()->format('d/m')) }}</div>
                        <div class="value empresa">{{ $note['cliente'] ?: '-' }}</div>
                        <div class="value ruc">{{ $note['ruc'] ?: '-' }}</div>
                        <div class="value telefono">{{ $note['telefono'] ?: '-' }}</div>
                        <div class="value servicio">{{ $note['servicio'] ?: '-' }}</div>
                        <div class="descripcion">{{ $note['descripcion'] ?: '-' }}</div>
                    </div>
                @endif
            @endfor
        </div>
    @endforeach
</body>
</html>
