<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mensajes Ocultos') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('messages.export.pdf', ['include_hidden' => 1]) }}" 
                   class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar PDF (Incluye Ocultos)
                </a>
                <a href="{{ route('messages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Ver Mensajes Activos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Mensajes Ocultos</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Estos mensajes están ocultos de la vista principal pero se mantienen guardados para exportación PDF. Puedes restaurarlos o eliminarlos permanentemente.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de mensajes ocultos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($messages->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Asunto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Destinatarios
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ocultado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($messages as $message)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $message->message_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ Str::limit($message->subject, 50) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit(strip_tags($message->content), 80) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $message->status_badge }}">
                                                    {{ ucfirst($message->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $message->recipients_count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div>{{ $message->hidden_at->format('d/m/Y') }}</div>
                                                <div class="text-xs">{{ $message->hidden_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('messages.show', $message) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                                    
                                                    <form method="POST" action="{{ route('messages.unhide', $message) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-green-600 hover:text-green-900"
                                                                onclick="return confirm('¿Restaurar este mensaje a la vista principal?')">
                                                            Restaurar
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" action="{{ route('messages.destroy', $message) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900"
                                                                onclick="return confirm('¿Eliminar permanentemente este mensaje? Esta acción no se puede deshacer.')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V9a2 2 0 01-2 2h-2m0 0v2a2 2 0 002 2h2a2 2 0 002-2v-2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mensajes ocultos</h3>
                            <p class="mt-1 text-sm text-gray-500">Los mensajes que ocultes aparecerán aquí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>