<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <title>Notas</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #111;
        }

        .page {
            position: relative;
            width: 1000px;
            height: 1414px;
            margin: 0 auto;
            page-break-after: always;
            background-image: url('{{ asset('images/notas.jpg') }}');
            background-repeat: no-repeat;
            background-position: center top;
            background-size: 1000px 1414px;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .page-month {
            position: absolute;
            top: 48px;
            left: 0;
            width: 1000px;
            text-align: center;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .note-slot {
            position: absolute;
            width: 350px;
            height: 520px;
        }

        .slot-1 { left: 84px; top: 224px; }
        .slot-2 { left: 554px; top: 224px; }
        .slot-3 { left: 84px; top: 815px; }
        .slot-4 { left: 554px; top: 815px; }

        .fecha {
            position: absolute;
            left: 0;
            top: 34px;
            width: 350px;
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .value {
            position: absolute;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.15;
            font-size: 19px;
        }

        .empresa  { left: 84px; top: 108px; width: 258px; }
        .ruc      { left: 34px; top: 158px; width: 308px; }
        .telefono { left: 34px; top: 214px; width: 108px; font-size: 14px; }
        .servicio { left: 154px; top: 214px; width: 188px; font-size: 14px; }

        .descripcion {
            position: absolute;
            left: 34px;
            top: 260px;
            width: 308px;
            height: 183px;
            overflow: hidden;
            line-height: 1.28;
            white-space: pre-line;
            font-size: 19px;
        }
    </style>
</head>
<body>
    @php($chunks = collect($notes)->chunk(4))

    @foreach ($chunks as $chunk)
        <div class="page">
            <div class="page-month">{{ mb_strtoupper($exportMonthTitle ?? '') }}</div>
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
