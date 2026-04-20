<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ver Mensaje') }}
            </h2>
            <div class="flex space-x-2">
                @if($message->status === 'draft')
                    <a href="{{ route('messages.edit', $message) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </a>
                @endif
                
                @if(in_array($message->status, ['draft', 'pending']))
                    <form method="POST" action="{{ route('messages.send', $message) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('¿Estás seguro de enviar este mensaje?')">
                            Enviar Ahora
                        </button>
                    </form>
                @endif
                
                <form method="POST" action="{{ route('messages.duplicate', $message) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Duplicar
                    </button>
                </form>
                
                <a href="{{ route('messages.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del mensaje -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Estado:</span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $message->status_badge }}">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tipo:</span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($message->type) }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Prioridad:</span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $message->priority_badge }}">
                                        {{ ucfirst($message->priority) }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Destinatarios:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $message->recipients_count }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tipo de destinatario:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ ucfirst($message->recipient_type) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Fechas</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Creado:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                @if($message->scheduled_at)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Programado para:</span>
                                        <span class="ml-2 text-sm text-gray-900">{{ $message->scheduled_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                                
                                @if($message->sent_at)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Enviado:</span>
                                        <span class="ml-2 text-sm text-gray-900">{{ $message->sent_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                                
                                @if($message->updated_at != $message->created_at)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Actualizado:</span>
                                        <span class="ml-2 text-sm text-gray-900">{{ $message->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido del mensaje -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contenido</h3>
                    
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-500">Asunto:</span>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $message->subject }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Mensaje:</span>
                        <div class="mt-1 p-4 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $message->content }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Destinatarios -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Destinatarios ({{ $message->recipients_count }})</h3>
                    
                    @if($message->recipients && count($message->recipients) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($message->recipients as $recipient)
                                <div class="bg-gray-50 px-3 py-2 rounded-md">
                                    <span class="text-sm text-gray-900">{{ $recipient }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No hay destinatarios configurados.</p>
                    @endif
                </div>
            </div>

            <!-- Estadísticas de envío -->
            @if($message->isSent() && isset($message->metadata['sent_count']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estadísticas de Envío</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $message->metadata['sent_count'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Enviados exitosamente</div>
                            </div>
                            
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $message->metadata['failed_count'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Fallos en envío</div>
                            </div>
                            
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ round((($message->metadata['sent_count'] ?? 0) / $message->recipients_count) * 100, 1) }}%
                                </div>
                                <div class="text-sm text-gray-500">Tasa de éxito</div>
                            </div>
                        </div>
                        
                        @if(isset($message->metadata['failed_emails']) && count($message->metadata['failed_emails']) > 0)
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 mb-2">Correos fallidos:</h4>
                                <div class="bg-red-50 p-3 rounded-md">
                                    @foreach($message->metadata['failed_emails'] as $failedEmail)
                                        <div class="text-sm text-red-700">{{ $failedEmail }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>