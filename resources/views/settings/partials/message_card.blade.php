<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 hover:border-vc-magenta/40 hover:shadow-md transition-all flex flex-col group relative overflow-hidden h-full">
    <!-- Color Banner Top -->
    <div class="absolute top-0 left-0 right-0 h-1 @if($message->type === 'email') bg-blue-400 @elseif($message->type === 'whatsapp') bg-green-400 @else bg-vc-purple @endif"></div>

    <div x-show="editingMessage !== {{ $message->id }}" class="flex-1 flex flex-col">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h5 class="font-bold text-gray-900 leading-tight mb-1 pr-4">{{ $message->title }}</h5>
                <div class="flex flex-wrap gap-1">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider
                        @if($message->type === 'email') bg-blue-50 text-blue-700 border border-blue-100
                        @elseif($message->type === 'whatsapp') bg-green-50 text-green-700 border border-green-100
                        @else bg-purple-50 text-purple-700 border border-purple-100
                        @endif">
                        @if($message->type === 'email') Correo
                        @elseif($message->type === 'whatsapp') WhatsApp
                        @else Email + WA
                        @endif
                    </span>
                    @if($message->usage && $message->usage !== 'general')
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                        {{ ucfirst($message->usage) }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex-1">
            <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed line-clamp-4 group-hover:line-clamp-none transition-all">{{ $message->content }}</p>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
            <button @click="editingMessage = {{ $message->id }}; editForm = { name: `{{ addslashes($message->title) }}`, content: `{{ addslashes($message->content) }}`, type: `{{ $message->type }}`, usage: `{{ $message->usage ?? 'general' }}` }" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            </button>
            <form action="{{ route('settings.custom-messages.delete', $message) }}" method="POST" onsubmit="return confirm('¿Eliminar de forma permanente este mensaje?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Editing Mode -->
    <div x-show="editingMessage === {{ $message->id }}" class="flex-1 flex flex-col" style="display: none;">
        <form action="{{ route('settings.custom-messages.update', $message) }}" method="POST" class="h-full flex flex-col">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <input type="text" name="name" x-model="editForm.name" class="w-full text-xs font-bold border-gray-300 rounded-lg focus:border-vc-magenta focus:ring-vc-magenta p-2 text-gray-900" required>
            </div>
            
            <div class="flex gap-2 mb-3">
                <select name="type" x-model="editForm.type" class="flex-1 text-[10px] border-gray-300 rounded-lg p-1.5 font-bold text-gray-900">
                    <option value="both">Email + WA</option>
                    <option value="email">Solo Email</option>
                    <option value="whatsapp">Solo WhatsApp</option>
                </select>
                <select name="usage" x-model="editForm.usage" class="flex-1 text-[10px] border-gray-300 rounded-lg p-1.5 font-bold text-gray-900">
                    <option value="general">Uso General</option>
                    <option value="quotation">Cotizaciones</option>
                    <option value="dashboard">Dashboard</option>
                </select>
            </div>

            <div class="flex-1 mb-4 relative">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-[10px] font-bold text-gray-500 uppercase">Contenido</label>
                    <button type="button" @click="insertEmoji('edit_content_{{ $message->id }}', $event)" class="text-gray-400 hover:text-vc-magenta transition-colors p-1" title="Insertar emoji">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                </div>
                <textarea id="edit_content_{{ $message->id }}" name="content" x-model="editForm.content" class="w-full h-32 text-xs border-gray-300 rounded-lg focus:border-vc-magenta focus:ring-vc-magenta p-2 leading-relaxed text-gray-900" required></textarea>
            </div>
            
            <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-100">
                <button type="button" @click="editingMessage = null" class="text-[10px] font-bold text-gray-400 hover:text-gray-600 px-2 py-1">Cancelar</button>
                <button type="submit" class="bg-gray-800 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-black transition-colors shadow-sm">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
