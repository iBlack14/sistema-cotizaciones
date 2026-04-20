@props(['limit' => 10, 'showHeader' => true])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="recentMessages({{ $limit }})">
    @if($showHeader)
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Mensajes Recientes</h3>
                <div class="flex space-x-2">
                    <button @click="refresh()" 
                            class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Actualizar
                    </button>
                    <a href="{{ route('messages.index') }}" 
                       class="text-sm text-gray-600 hover:text-gray-900">
                        Ver todos
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="p-6">
        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Cargando mensajes...</p>
        </div>

        <!-- Messages List -->
        <div x-show="!loading">
            <template x-if="messages.length === 0">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V9a2 2 0 01-2 2h-2m0 0v2a2 2 0 002 2h2a2 2 0 002-2v-2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mensajes recientes</h3>
                    <p class="mt-1 text-sm text-gray-500">Crea tu primer mensaje para comenzar.</p>
                    <div class="mt-6">
                        <a href="{{ route('messages.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Crear Mensaje
                        </a>
                    </div>
                </div>
            </template>

            <div class="space-y-3">
                <template x-for="message in messages" :key="message.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Header with number and status -->
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        #<span x-text="message.message_number"></span>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                          :class="message.status_badge"
                                          x-text="message.status.charAt(0).toUpperCase() + message.status.slice(1)">
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                          :class="message.priority_badge"
                                          x-text="message.type.charAt(0).toUpperCase() + message.type.slice(1)">
                                    </span>
                                </div>

                                <!-- Subject -->
                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="message.subject"></h4>
                                
                                <!-- Content preview -->
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2" x-text="message.content"></p>
                                
                                <!-- Meta info -->
                                <div class="mt-2 flex items-center text-xs text-gray-400 space-x-4">
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2m2 4h6m-6 4h6m-3-8V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4"></path>
                                        </svg>
                                        <span x-text="message.recipients_count"></span> destinatarios
                                    </span>
                                    <span x-text="message.created_at_human"></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Ver -->
                                <a :href="`{{ route('messages.index') }}/${message.id}`" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50"
                                   title="Ver mensaje">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <!-- Ocultar -->
                                <button @click="hideMessage(message.id)" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50"
                                        title="Ocultar mensaje">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Ver más -->
            <template x-if="messages.length >= limit">
                <div class="mt-4 text-center">
                    <a href="{{ route('messages.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Ver todos los mensajes
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function recentMessages(limit = 10) {
    return {
        messages: [],
        loading: true,
        limit: limit,

        init() {
            this.loadMessages();
        },

        async loadMessages() {
            this.loading = true;
            try {
                const response = await fetch(`{{ route('api.messages.recent') }}?limit=${this.limit}`);
                const data = await response.json();
                this.messages = data;
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.loading = false;
            }
        },

        async hideMessage(messageId) {
            if (!confirm('¿Estás seguro de ocultar este mensaje? Se mantendrá guardado para exportación.')) {
                return;
            }

            try {
                const response = await fetch(`{{ route('messages.index') }}/${messageId}/hide`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    // Remover el mensaje de la lista
                    this.messages = this.messages.filter(m => m.id !== messageId);
                    
                    // Mostrar notificación de éxito
                    this.showNotification('Mensaje ocultado exitosamente', 'success');
                } else {
                    this.showNotification('Error al ocultar el mensaje', 'error');
                }
            } catch (error) {
                console.error('Error hiding message:', error);
                this.showNotification('Error al ocultar el mensaje', 'error');
            }
        },

        refresh() {
            this.loadMessages();
        },

        showNotification(message, type = 'info') {
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>