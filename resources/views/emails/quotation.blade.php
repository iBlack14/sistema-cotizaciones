<!DOCTYPE html>
<html>
<head>
    <title>Cotización</title>
</head>
<body>
    @if(isset($body) && $body)
        {!! nl2br(e($body)) !!}
    @else
        <h2>Hola {{ $quotation->client_company ?? $quotation->client_name }},</h2>
        <p>Adjunto encontrará la cotización solicitada.</p>
        <p>Si tiene alguna pregunta, no dude en contactarnos.</p>
        <br>
        <p>Saludos,</p>
        <p>{{ config('app.name') }}</p>
    @endif
</body>
</html>
