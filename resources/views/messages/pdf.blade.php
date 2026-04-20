<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mensajes - VIA COMUNICATIVA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #BF1F6A;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #BF1F6A;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #BF1F6A;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #BF1F6A;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .message-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .message-number {
            background-color: #BF1F6A;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .message-badges {
            display: flex;
            gap: 5px;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-status-draft { background-color: #f3f4f6; color: #374151; }
        .badge-status-sent { background-color: #d1fae5; color: #065f46; }
        .badge-status-pending { background-color: #fef3c7; color: #92400e; }
        .badge-status-failed { background-color: #fee2e2; color: #991b1b; }
        .badge-status-delivered { background-color: #dbeafe; color: #1e40af; }
        
        .badge-priority-low { background-color: #f3f4f6; color: #6b7280; }
        .badge-priority-normal { background-color: #dbeafe; color: #2563eb; }
        .badge-priority-high { background-color: #fed7aa; color: #ea580c; }
        .badge-priority-urgent { background-color: #fee2e2; color: #dc2626; }
        
        .badge-type { background-color: #e0e7ff; color: #3730a3; }
        
        .message-subject {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .message-content {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 10px;
            white-space: pre-wrap;
            font-size: 11px;
            line-height: 1.5;
        }
        
        .message-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            font-size: 10px;
            color: #6b7280;
        }
        
        .message-meta strong {
            color: #374151;
        }
        
        .recipients-list {
            margin-top: 8px;
            padding: 8px;
            background-color: #f3f4f6;
            border-radius: 3px;
        }
        
        .recipients-list h5 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #374151;
            text-transform: uppercase;
        }
        
        .recipient {
            display: inline-block;
            background-color: white;
            padding: 2px 6px;
            margin: 2px;
            border-radius: 2px;
            font-size: 9px;
            border: 1px solid #d1d5db;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; }
            .message-item { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE DE MENSAJES</h1>
        <p><strong>VIA COMUNICATIVA</strong></p>
        <p>Sistema de Gestión de Cotizaciones</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <h3>Resumen del Reporte</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $messages->count() }}</div>
                <div class="summary-label">Total Mensajes</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $messages->where('status', 'sent')->count() }}</div>
                <div class="summary-label">Enviados</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $messages->where('status', 'draft')->count() }}</div>
                <div class="summary-label">Borradores</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $messages->where('is_hidden', true)->count() }}</div>
                <div class="summary-label">Ocultos</div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @foreach($messages as $index => $message)
        @if($index > 0 && $index % 3 == 0)
            <div class="page-break"></div>
        @endif
        
        <div class="message-item">
            <div class="message-header">
                <div class="message-number">#{{ $message->message_number ?? ($index + 1) }}</div>
                <div class="message-badges">
                    <span class="badge badge-status-{{ $message->status }}">{{ ucfirst($message->status) }}</span>
                    <span class="badge badge-priority-{{ $message->priority }}">{{ ucfirst($message->priority) }}</span>
                    <span class="badge badge-type">{{ ucfirst($message->type) }}</span>
                    @if($message->is_hidden)
                        <span class="badge" style="background-color: #fca5a5; color: #7f1d1d;">Oculto</span>
                    @endif
                </div>
            </div>

            <div class="message-subject">{{ $message->subject }}</div>

            <div class="message-content">{{ $message->content }}</div>

            <div class="message-meta">
                <div>
                    <strong>Creado:</strong><br>
                    {{ $message->created_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Destinatarios:</strong><br>
                    {{ is_array($message->recipients) ? count($message->recipients) : 0 }}
                </div>
                <div>
                    <strong>Tipo Destinatario:</strong><br>
                    {{ ucfirst($message->recipient_type) }}
                </div>
            </div>

            @if($message->scheduled_at)
                <div class="message-meta" style="margin-top: 8px;">
                    <div>
                        <strong>Programado para:</strong><br>
                        {{ $message->scheduled_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            @endif

            @if($message->sent_at)
                <div class="message-meta" style="margin-top: 8px;">
                    <div>
                        <strong>Enviado:</strong><br>
                        {{ $message->sent_at->format('d/m/Y H:i') }}
                    </div>
                    @if(isset($message->metadata['sent_count']))
                        <div>
                            <strong>Exitosos:</strong><br>
                            {{ $message->metadata['sent_count'] ?? 0 }}
                        </div>
                        <div>
                            <strong>Fallidos:</strong><br>
                            {{ $message->metadata['failed_count'] ?? 0 }}
                        </div>
                    @endif
                </div>
            @endif

            @if($message->is_hidden && $message->hidden_at)
                <div class="message-meta" style="margin-top: 8px;">
                    <div>
                        <strong>Ocultado:</strong><br>
                        {{ $message->hidden_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            @endif

            @if($message->recipients && count($message->recipients) > 0)
                <div class="recipients-list">
                    <h5>Destinatarios:</h5>
                    @foreach($message->recipients as $recipient)
                        <span class="recipient">{{ $recipient }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    @if($messages->count() == 0)
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <p>No se encontraron mensajes para exportar.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>VIA COMUNICATIVA</strong> - Sistema de Gestión de Cotizaciones</p>
        <p>Reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</p>
        <p>Total de mensajes en el reporte: {{ $messages->count() }}</p>
    </div>
</body>
</html>