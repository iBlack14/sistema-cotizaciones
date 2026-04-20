<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Mensajes') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('messages.export.pdf') }}"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- SECCIÓN 1: WHATSAPP (MOVIDA AL INICIO) -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-purple-200">
                <div class="px-6 py-5 border-b border-purple-200 bg-gradient-to-r from-[#8704BF] to-[#F2059F]">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                </svg>
                                WhatsApp
                            </h3>
                            <p class="text-purple-100 text-sm font-medium">Envía mensajes directamente a WhatsApp</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gradient-to-br from-purple-50 to-pink-50">
                    <div class="max-w-6xl mx-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                            <!-- LEFT COLUMN: Compose Area (8 cols) -->
                            <div class="lg:col-span-8 space-y-6">

                                <!-- Section: Recipient -->
                                <div class="bg-white p-6 rounded-xl border-2 border-purple-100 shadow-sm">
                                    <label for="whatsapp_number"
                                        class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-full bg-purple-100 text-[#8704BF] flex items-center justify-center mr-2 text-xs">1</span>
                                        Número de Destino
                                    </label>
                                    <div class="relative max-w-md">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                            <span class="text-[#8704BF] text-base font-bold">+51</span>
                                            <div class="h-6 w-px bg-gray-300 mx-3"></div>
                                        </div>
                                        <input type="tel" id="whatsapp_number" placeholder="987654321"
                                            class="pl-20 block w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-[#F2059F] focus:ring-[#F2059F] text-gray-900 placeholder-gray-400 font-bold text-lg h-12 bg-gray-50 focus:bg-white transition-colors"
                                            maxlength="9">
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 ml-1">Ingresa el número de celular (9 dígitos)
                                    </p>
                                </div>

                                <!-- Section: Message Content -->
                                <div class="bg-white p-6 rounded-xl border-2 border-purple-100 shadow-sm relative">
                                    <label for="message_content"
                                        class="block text-sm font-bold text-gray-800 mb-2 flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-full bg-purple-100 text-[#8704BF] flex items-center justify-center mr-2 text-xs">2</span>
                                        Contenido del Mensaje
                                    </label>

                                    <!-- Variables Button Bar -->
                                    <div class="mb-2 flex space-x-2">
                                        <button onclick="insertVariable(String.fromCharCode(123,123)+'saludo'+String.fromCharCode(125,125))"
                                            class="px-3 py-1 text-xs bg-purple-100 text-[#5F1BF2] rounded-full hover:bg-purple-200 transition-colors font-semibold border border-purple-200 flex items-center shadow-sm">
                                            <span class="mr-1">🤖</span> Saludo Automático
                                        </button>
                                        <div class="text-[10px] text-gray-400 flex items-center ml-2 italic">
                                            Se convierte en Buenos días/tardes/noches al enviar
                                        </div>
                                    </div>

                                    <textarea id="message_content" rows="8"
                                        class="w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-[#F2059F] focus:ring-[#F2059F] text-gray-900 placeholder-gray-400 font-medium p-4 bg-gray-50 focus:bg-white transition-colors resize-none text-base"
                                        placeholder="Escribe tu mensaje aquí..."></textarea>
                                    <div id="flyer_selected_badge"
                                        class="hidden mt-3 rounded-lg border border-fuchsia-300 bg-fuchsia-50 px-3 py-2 text-xs text-gray-900">
                                        <div class="flex items-center justify-between gap-2">
                                            <span id="flyer_selected_text" class="font-semibold text-sm text-gray-900">Flayer seleccionado</span>
                                            <button type="button" onclick="clearSelectedFlyer()"
                                                class="rounded bg-white px-2 py-1 text-[11px] font-bold text-fuchsia-700 border border-fuchsia-200 hover:bg-fuchsia-100">
                                                Quitar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <button onclick="openSaveTemplateModal()"
                                            class="flex-1 bg-white border-2 border-[#5F1BF2] text-[#5F1BF2] hover:bg-purple-50 px-6 py-3 rounded-lg text-sm font-bold flex items-center justify-center transition-all duration-200">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                                </path>
                                            </svg>
                                            Guardar Mensaje
                                        </button>
                                        <button onclick="sendToWhatsApp()"
                                            class="flex-1 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] hover:shadow-[#8704BF]/40 text-white px-6 py-3 rounded-lg text-sm font-bold flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                            </svg>
                                            Enviar Mensaje
                                        </button>
                                    </div>
                                </div>

                                <!-- Section: Recent Numbers -->
                                <div class="mt-6">
                                    <h4
                                        class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Recientes
                                    </h4>
                                    <div class="flex flex-wrap gap-2" id="recent_numbers">
                                        <!-- Updated via JS -->
                                    </div>
                                </div>

                                <!-- Hidden Preview Container (Shows relative to this col) -->
                                <div id="message_preview" class="hidden mt-4">
                                    <div class="bg-gray-100 rounded-xl p-4 border border-gray-200">
                                        <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Vista Previa</h5>
                                        <div class="bg-white p-4 rounded-lg shadow-sm">
                                            <p class="text-gray-800 whitespace-pre-wrap" id="preview_content"></p>
                                        </div>
                                        <div class="mt-2 text-right">
                                            <span class="text-xs text-gray-500 font-medium">Se enviará a: +51 <span
                                                    id="preview_number"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN: Templates Sidebar (4 cols) -->
                            <div class="lg:col-span-4">
                                <div
                                    class="bg-white rounded-xl border-2 border-purple-100 shadow-sm flex flex-col overflow-hidden">
                                    <!-- Header -->
                                    <button type="button" onclick="toggleTemplatesAccordion()"
                                        class="w-full bg-purple-50 px-4 py-3 border-b border-purple-100 flex justify-between items-center text-left hover:bg-purple-100/60 transition-colors">
                                        <h3 class="font-bold text-[#5F1BF2] flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                            Plantillas
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="bg-white text-[#5F1BF2] text-xs font-bold px-2 py-1 rounded-full shadow-sm">{{ $predefinedMessages->count() }}</span>
                                            <svg id="templates_accordion_icon" class="h-4 w-4 text-[#5F1BF2] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </button>

                                    <div id="templates_accordion_body">

                                    <!-- Search -->
                                    <div class="p-3 border-b border-gray-100">
                                        <div class="relative">
                                            <input type="text" id="template_search" placeholder="Buscar..."
                                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:border-[#5F1BF2] focus:ring-[#5F1BF2] text-gray-900 placeholder-gray-400">
                                            <svg class="w-4 h-4 mr-2 text-gray-400 absolute left-3 top-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- List -->
                                    <div class="flex-1 overflow-y-auto max-h-[500px] p-2 space-y-2 bg-gray-50">
                                        @if($predefinedMessages->count() > 0)
                                            @foreach($predefinedMessages as $message)
                                                <div onclick="fillMessageFromTemplateByElement(this)" data-template-id="{{ $message->id }}"
                                                    class="template-card group bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:border-[#5F1BF2] hover:shadow-md cursor-pointer transition-all duration-200 relative overflow-hidden">
                                                    <div
                                                        class="absolute top-0 left-0 w-1 h-full bg-[#5F1BF2] opacity-0 group-hover:opacity-100 transition-opacity">
                                                    </div>
                                                    <div class="flex justify-between items-start mb-1">
                                                        <h4
                                                            class="font-bold text-gray-800 text-sm group-hover:text-[#5F1BF2] transition-colors">
                                                            {{ $message->title }}
                                                        </h4>
                                                        <span
                                                            class="text-[10px] font-mono text-gray-400 bg-gray-100 px-1 rounded">#{{ $message->number }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                                        {{ $message->content }}
                                                    </p>
                                                    <div
                                                        class="mt-2 flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <span class="text-[10px] font-bold text-[#5F1BF2] flex items-center">
                                                            Usar
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-8 text-gray-400">
                                                <p class="text-sm">No hay plantillas guardadas</p>
                                            </div>
                                        @endif
                                    </div>
                                    </div>
                                </div>

                                <div class="mt-4 bg-white rounded-xl border-2 border-pink-100 shadow-sm overflow-hidden">
                                    <button type="button" onclick="toggleFlyersAccordion()"
                                        class="w-full bg-pink-50 px-4 py-3 border-b border-pink-100 flex items-center justify-between text-left hover:bg-pink-100/60 transition-colors">
                                        <h3 class="font-bold text-[#F2059F] flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Flayers
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="bg-white text-[#F2059F] text-xs font-bold px-2 py-1 rounded-full shadow-sm">{{ $flyers->count() }}</span>
                                            <svg id="flyers_accordion_icon" class="h-4 w-4 text-[#F2059F] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </button>

                                    <div id="flyers_accordion_body">
                                    <div class="p-3 border-b border-gray-100 bg-gray-50">
                                        <div class="rounded-lg border border-dashed border-pink-200 bg-white px-4 py-3 text-xs text-gray-600">
                                            Los flayers se comparten ahora como enlace dentro del mensaje de WhatsApp. Ya no se necesita QR ni sesion externa.
                                        </div>
                                    </div>

                                    <div class="p-3 border-b border-gray-100">
                                        <form action="{{ route('messages.flyers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                            @csrf
                                            <input type="text" name="title" required placeholder="Nombre del flayer"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-[#F2059F] focus:ring-[#F2059F]">
                                            <textarea name="caption" rows="2" placeholder="Texto del flayer (opcional)"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-[#F2059F] focus:ring-[#F2059F]"></textarea>
                                            <input type="file" name="image" required accept="image/*"
                                                class="w-full text-xs text-gray-600 file:mr-2 file:rounded-full file:border-0 file:bg-pink-100 file:px-3 file:py-1 file:text-xs file:font-bold file:text-pink-700 hover:file:bg-pink-200">
                                            <button type="submit" class="w-full rounded-lg bg-pink-600 px-3 py-2 text-sm font-bold text-white hover:bg-pink-700">
                                                Guardar Flayer
                                            </button>
                                        </form>
                                    </div>

                                    <div id="flyers_list" class="max-h-[320px] overflow-y-auto bg-gray-50 p-2 space-y-2">
                                        @forelse($flyers as $flyer)
                                            <div class="rounded-lg border border-gray-200 bg-white p-2">
                                                <div class="flex gap-2">
                                                    <img src="{{ $flyer->url }}" alt="{{ $flyer->title }}" class="h-20 w-20 rounded object-cover bg-gray-50 border border-gray-100 flex-shrink-0">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-xs font-bold text-gray-800" title="{{ $flyer->title }}">{{ $flyer->title }}</p>
                                                        <p class="mt-1 line-clamp-3 text-[11px] text-gray-500">{{ $flyer->caption ?: 'Sin texto' }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex gap-2">
                                                    <button type="button"
                                                        data-flyer-url="{{ $flyer->url }}"
                                                        data-flyer-title="{{ $flyer->title }}"
                                                        data-flyer-caption="{{ $flyer->caption ?? '' }}"
                                                        onclick="useFlyerFromButton(this)"
                                                        class="flex-1 rounded bg-purple-100 px-2 py-1 text-[11px] font-bold text-purple-700 hover:bg-purple-200">
                                                        Usar Flayer
                                                    </button>
                                                    <form action="{{ route('messages.flyers.delete', $flyer) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full rounded bg-red-100 px-2 py-1 text-[11px] font-bold text-red-700 hover:bg-red-200" onclick="return confirm('¿Eliminar este flayer?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="py-6 text-center text-xs text-gray-500">Aún no hay flayers guardados.</p>
                                        @endforelse
                                    </div>
                                    <div class="border-t border-gray-100 bg-white p-2">
                                        <button type="button" id="flyers_toggle_more_btn" onclick="toggleFlyersListSize()"
                                            class="w-full rounded-lg border border-pink-200 bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700 hover:bg-pink-100">
                                            Ver más flayers
                                        </button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Select for compatibility -->
                            <select id="message_select" class="hidden">
                                <option value="">-- Custom --</option>
                                @foreach($predefinedMessages as $message)
                                    <option value="{{ $message->id }}" data-content="{{ $message->content }}">
                                        {{ $message->title }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: MENSAJES RECIENTES -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-purple-200">
                <div class="px-6 py-5 border-b border-purple-200 bg-gradient-to-r from-purple-500 to-pink-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mensajes Recientes
                            </h3>
                            <p class="text-purple-100 text-sm font-medium">Últimos mensajes enviados y programados</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('messages.hidden') }}"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                Ver Ocultos ({{ $hiddenCount }})
                            </a>
                            <button onclick="refreshRecentMessages()"
                                class="bg-white text-purple-600 hover:bg-purple-50 px-4 py-2 rounded-lg text-sm font-bold flex items-center shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gradient-to-br from-purple-50 to-pink-50" id="recent-messages-container">
                    <!-- Los mensajes recientes se cargarán aquí via JavaScript -->
                    <div class="text-center py-12" id="loading-recent">
                        <svg class="animate-spin h-10 w-10 text-purple-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="mt-3 text-sm text-purple-600 font-medium">Cargando mensajes recientes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver mensaje predeterminado -->
    <div id="predefinedModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Mensaje Predeterminado</h3>
                        <button onclick="closePredefinedModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="whitespace-pre-wrap text-sm text-gray-700" id="modalContent"></div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                    <button onclick="closePredefinedModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                        Cerrar
                    </button>
                    <button onclick="copyPredefinedMessage()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Copiar Texto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script id="predefinedMessagesJson" type="application/json">@json($predefinedMessages)</script>
    <script>
        let predefinedMessages = JSON.parse(document.getElementById('predefinedMessagesJson')?.textContent || '[]');
        let currentPredefinedMessage = null;
        let selectedFlyerUrl = null;
        let selectedFlyerTitle = '';
        let selectedFlyerCaption = '';
        let flyersAccordionOpen = true;
        let flyersListExpanded = false;
        let templatesAccordionOpen = true;

        // Cargar mensajes recientes al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            loadRecentMessages();
            loadRecentNumbers();
            initializeMessageGrid();
            applyFlyersUiState();
            applyTemplatesUiState();

            // Agregar evento para limpiar vista previa cuando cambie la selección
            const messageSelectEl = document.getElementById('message_select');
            if (messageSelectEl) {
                messageSelectEl.addEventListener('change', function () {
                    if (!this.value) {
                        document.getElementById('message_preview').classList.add('hidden');
                    }
                });
            }

            // Evento de búsqueda
            const searchInput = document.getElementById('template_search') || document.getElementById('message_search');
            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    filterTemplateCards(e.target.value);
                });
            }

            // Formatear número mientras se escribe
            document.getElementById('whatsapp_number').addEventListener('input', function (e) {
                // Solo permitir números
                this.value = this.value.replace(/\D/g, '');

                // Limpiar vista previa si cambia el número
                document.getElementById('message_preview').classList.add('hidden');
            });

            // Permitir enviar con Enter
            document.getElementById('whatsapp_number').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    previewWhatsAppMessage();
                }
            });
        });

        // Global variables for filtering
        let selectedMessageId = null;

        // Insertar variable en el textarea
        function insertVariable(text) {
            const textarea = document.getElementById('message_content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const currentText = textarea.value;

            const newText = currentText.substring(0, start) + text + currentText.substring(end);
            textarea.value = newText;

            // Reposition cursor after inserted text
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
        }

        let currentCategory = 'all';
        let filteredMessages = [...predefinedMessages];

        // Inicializar grid de mensajes
        function initializeMessageGrid() {
            const container = document.getElementById('message_list');
            if (container) {
                renderMessageGrid(predefinedMessages);
            }
        }

        // Renderizar grid de mensajes
        function renderMessageGrid(messages) {
            const container = document.getElementById('message_list');
            if (!container) return;

            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <p class="text-sm font-medium">No se encontraron mensajes</p>
                    </div>
                `;
                return;
            }

            const messagesHtml = messages.map(message => `
                <div class="message-item p-3 border border-gray-200 rounded-lg hover:border-[#F2059F] hover:bg-purple-50 cursor-pointer transition-all duration-200 ${selectedMessageId === message.id ? 'border-[#8704BF] bg-purple-100' : ''}"
                     onclick="selectMessage(${message.id})">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-[#8704BF] text-white text-xs font-bold rounded-full">
                                    ${message.number}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getTypeBadgeClass(message.type)}">
                                    ${getTypeIcon(message.type)} ${ucfirst(message.type)}
                                </span>
                                ${message.is_favorite ? '<span class="text-yellow-500">⭐</span>' : ''}
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 truncate">${message.title || 'Sin título'}</h4>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">${(message.content || '').substring(0, 80)}...</p>
                        </div>
                        <div class="flex space-x-1 ml-2">
                            <button onclick="event.stopPropagation(); quickPreview(${message.id})" 
                                    class="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors duration-200"
                                    title="Vista previa rápida">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="event.stopPropagation(); toggleFavorite(${message.id})" 
                                    class="p-1 ${message.is_favorite ? 'text-yellow-500' : 'text-gray-400'} hover:text-yellow-500 hover:bg-yellow-50 rounded transition-colors duration-200"
                                    title="Marcar como favorito">
                                <svg class="w-4 h-4" fill="${message.is_favorite ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = messagesHtml;
        }

        // Filtrar mensajes
        function filterMessages(category, event = null) {
            currentCategory = category;
            const searchInput = document.getElementById('template_search') || document.getElementById('message_search');
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

            // Actualizar tabs activas solo si hay un evento (click en tab)
            if (event && event.target) {
                document.querySelectorAll('.category-tab').forEach(tab => {
                    tab.classList.remove('bg-green-600', 'text-white');
                    tab.classList.add('bg-gray-200', 'text-gray-700');
                });

                event.target.classList.remove('bg-gray-200', 'text-gray-700');
                event.target.classList.add('bg-[#8704BF]', 'text-white');
            }

            // Filtrar mensajes
            filteredMessages = predefinedMessages.filter(message => {
                let matchesCategory = true;
                let matchesSearch = true;

                // Filtrar por categoría
                if (category === 'favoritos') {
                    matchesCategory = message.is_favorite;
                } else if (category === 'recientes') {
                    // Últimos 7 días (simulado)
                    const sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
                    matchesCategory = new Date(message.updated_at) > sevenDaysAgo;
                } else if (category !== 'all') {
                    matchesCategory = message.type === category;
                }

                // Filtrar por búsqueda
                if (searchTerm) {
                    matchesSearch = message.title.toLowerCase().includes(searchTerm) ||
                        message.content.toLowerCase().includes(searchTerm) ||
                        message.number.toString().includes(searchTerm);
                }

                return matchesCategory && matchesSearch;
            });

            renderMessageGrid(filteredMessages);
        }

        function filterTemplateCards(rawTerm) {
            const term = (rawTerm || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.template-card');

            cards.forEach(card => {
                const content = card.textContent.toLowerCase();
                card.style.display = content.includes(term) ? '' : 'none';
            });
        }

        function selectMessage(id) {
            selectedMessageId = id;
            const message = predefinedMessages.find(m => m.id === id);

            if (message) {
                // Actualizar select hidden
                document.getElementById('message_select').value = id;

                // Actualizar visualización
                renderMessageGrid(filteredMessages);

                // Mostrar vista previa automáticamente
                showQuickPreview(message);
            }
        }

        // Vista previa rápida
        function quickPreview(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                showQuickPreview(message);
            }
        }

        // Mostrar vista previa rápida
        function showQuickPreview(message) {
            const number = document.getElementById('whatsapp_number').value.trim();

            if (number) {
                document.getElementById('preview_content').textContent = message.content;
                document.getElementById('preview_number').textContent = number;
                document.getElementById('message_preview').classList.remove('hidden');
            }
        }

        // Toggle favorito
        function toggleFavorite(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                message.is_favorite = !message.is_favorite;

                // Guardar en servidor (simulado)
                fetch(`/messages/predefined/${id}/favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).catch(error => console.log('Error guardando favorito:', error));

                renderMessageGrid(filteredMessages);
            }
        }

        // Funciones auxiliares
        function getTypeBadgeClass(type) {
            const classes = {
                'email': 'bg-blue-100 text-blue-800',
                'whatsapp': 'bg-green-100 text-green-800',
                'both': 'bg-purple-100 text-purple-800',
                'sms': 'bg-yellow-100 text-yellow-800',
                'notification': 'bg-red-100 text-red-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        }

        function getTypeIcon(type) {
            const icons = {
                'email': '📧',
                'whatsapp': '💬',
                'both': '🔄',
                'sms': '📱',
                'notification': '🔔'
            };
            return icons[type] || '📄';
        }

        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Ver mensaje predeterminado completo
        function viewPredefinedMessage(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                currentPredefinedMessage = message;
                document.getElementById('modalTitle').textContent = `#${message.number} - ${message.title}`;
                document.getElementById('modalContent').textContent = message.content;
                document.getElementById('predefinedModal').classList.remove('hidden');
            }
        }

        // Cerrar modal
        function closePredefinedModal() {
            document.getElementById('predefinedModal').classList.add('hidden');
            currentPredefinedMessage = null;
        }

        // Copiar mensaje predeterminado
        function copyPredefinedMessage() {
            if (currentPredefinedMessage) {
                navigator.clipboard.writeText(currentPredefinedMessage.content).then(() => {
                    showNotification('Mensaje copiado al portapapeles', 'success');
                });
            }
        }

        // Usar plantilla predeterminada
        function usePredefinedMessage(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                // Redirigir a crear mensaje con la plantilla
                window.location.href = `{{ route('messages.create') }}?template=${id}`;
            }
        }

        // Populate message from template
        function fillMessageFromTemplate(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                const textarea = document.getElementById('message_content');
                textarea.value = message.content;
                // Trigger input event to resize/validate if needed
                textarea.dispatchEvent(new Event('input'));

                // Select in dropdown for compatibility (optional)
                const select = document.getElementById('message_select');
                if (select) select.value = id;
            }
        }

        function fillMessageFromTemplateByElement(el) {
            if (!el) return;
            const id = Number(el.dataset.templateId || 0);
            if (id) fillMessageFromTemplate(id);
        }

        function useFlyer(url, title, caption) {
            selectedFlyerUrl = url;
            selectedFlyerTitle = (title || '').toString().trim() || 'Flayer';
            selectedFlyerCaption = caption || '';

            const badge = document.getElementById('flyer_selected_badge');
            const label = document.getElementById('flyer_selected_text');
            if (badge && label) {
                label.textContent = `Flayer seleccionado: ${selectedFlyerTitle}`;
                label.style.color = '#111827';
                label.style.fontSize = '14px';
                badge.classList.remove('hidden');
            }

            const textarea = document.getElementById('message_content');
            if (textarea && !textarea.value.trim() && selectedFlyerCaption) {
                textarea.value = selectedFlyerCaption;
            }
        }

        function useFlyerFromButton(btn) {
            if (!btn) return;
            useFlyer(
                btn.dataset.flyerUrl || '',
                btn.dataset.flyerTitle || '',
                btn.dataset.flyerCaption || ''
            );
        }

        function clearSelectedFlyer() {
            selectedFlyerUrl = null;
            selectedFlyerTitle = '';
            selectedFlyerCaption = '';
            const badge = document.getElementById('flyer_selected_badge');
            if (badge) badge.classList.add('hidden');
        }

        function applyFlyersUiState() {
            const body = document.getElementById('flyers_accordion_body');
            const icon = document.getElementById('flyers_accordion_icon');
            const list = document.getElementById('flyers_list');
            const btn = document.getElementById('flyers_toggle_more_btn');

            if (body) body.classList.toggle('hidden', !flyersAccordionOpen);
            if (icon) icon.classList.toggle('rotate-180', flyersAccordionOpen);
            if (list) list.classList.toggle('max-h-[320px]', !flyersListExpanded);
            if (list) list.classList.toggle('max-h-[620px]', flyersListExpanded);
            if (btn) btn.textContent = flyersListExpanded ? 'Ver menos flayers' : 'Ver más flayers';
        }

        function toggleFlyersAccordion() {
            flyersAccordionOpen = !flyersAccordionOpen;
            applyFlyersUiState();
        }

        function toggleFlyersListSize() {
            flyersListExpanded = !flyersListExpanded;
            applyFlyersUiState();
        }

        function applyTemplatesUiState() {
            const body = document.getElementById('templates_accordion_body');
            const icon = document.getElementById('templates_accordion_icon');
            if (body) body.classList.toggle('hidden', !templatesAccordionOpen);
            if (icon) icon.classList.toggle('rotate-180', templatesAccordionOpen);
        }

        function toggleTemplatesAccordion() {
            templatesAccordionOpen = !templatesAccordionOpen;
            applyTemplatesUiState();
        }

        // Guardar mensaje como plantilla (Modal)
        function openSaveTemplateModal() {
            const content = document.getElementById('message_content').value.trim();
            if (!content) {
                alert('Por favor escribe un mensaje para guardar.');
                return;
            }
            document.getElementById('template_content').value = content;
            document.getElementById('template_content_preview').textContent = content;
            document.getElementById('saveTemplateModal').classList.remove('hidden');
        }

        function closeSaveTemplateModal() {
            document.getElementById('saveTemplateModal').classList.add('hidden');
        }

        // Vista previa del mensaje de WhatsApp
        function previewWhatsAppMessage() {
            const number = document.getElementById('whatsapp_number').value.trim();
            const content = document.getElementById('message_content').value.trim();

            if (!number) {
                showNotification('Por favor ingresa un número de WhatsApp', 'error');
                return;
            }

            if (!content) {
                showNotification('Por favor escribe un mensaje o selecciona una plantilla', 'error');
                return;
            }

            // Validar número (solo dígitos, 9 caracteres para Perú +51, or 10 if standard)
            // Perú usually 9 digits for mobile. The placeholder was 10 digits before. 
            // Let's assume 9 or 10 digits to be safe or keep existing constraint.
            // Previous code had 10 digits constraint. Peru mobile numbers are 9 digits.
            // I'll update it to allow 9 digits.
            if (!/^\d{9,10}$/.test(number)) {
                showNotification('El número debe tener 9 o 10 dígitos', 'error');
                return;
            }

            // Resolver variables para vista previa
            const hour = new Date().getHours();
            let greeting = "Hola";
            if (hour >= 5 && hour < 12) greeting = "Buenos días";
            else if (hour >= 12 && hour < 19) greeting = "Buenas tardes";
            else greeting = "Buenas noches";

            const finalContent = content.replace(/@?\{\{saludo\}\}/gi, greeting).replace(/\(saludo\)/gi, greeting);

            // Mostrar vista previa
            document.getElementById('preview_content').textContent = finalContent;
            document.getElementById('preview_number').textContent = number;
            document.getElementById('message_preview').classList.remove('hidden');

            // Guardar número en historial
            saveRecentNumber(number);
        }

        // Enviar mensaje a WhatsApp
        function sendToWhatsApp() {
            const number = document.getElementById('whatsapp_number').value.trim();
            const content = document.getElementById('message_content').value.trim();

            // Try to get ID if it was selected from a template (optional logic)
            const messageSelect = document.getElementById('message_select');
            const messageId = messageSelect && messageSelect.value ? messageSelect.value : null;

            if (!number) {
                showNotification('Por favor ingresa un número de WhatsApp', 'error');
                return;
            }

            if (!content) {
                showNotification('Por favor escribe un mensaje o selecciona una plantilla', 'error');
                return;
            }

            // Validar número
            if (!/^\d{9,10}$/.test(number)) {
                showNotification('El número debe tener 9 o 10 dígitos', 'error');
                return;
            }

            const whatsappNumber = `51${number}`;

            // Registrar el envío en el sistema si es una plantilla, o como mensaje custom
            // We need to adjust the backend endpoint to handle custom messages if message_id is null
            // For now, we'll only log if messageId exists, or we leave it as is if backend requires ID.
            // But user said "SOLO ENVIAR". 

            if (messageId) {
                fetch('{{ route("messages.whatsapp.log") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        number: number,
                        message_id: messageId
                    })
                }).then(response => response.json())
                    .catch(error => console.error('Error logs:', error));
            }

            // Resolver variables (Saludo Automático)
            const hour = new Date().getHours();
            let greeting = "Hola";
            if (hour >= 5 && hour < 12) greeting = "Buenos días";
            else if (hour >= 12 && hour < 19) greeting = "Buenas tardes";
            else greeting = "Buenas noches";

            // Reemplazar tanto @{{saludo}} como (saludo)
            let finalContent = content.replace(/@?\{\{saludo\}\}/gi, greeting).replace(/\(saludo\)/gi, greeting);

            if (selectedFlyerUrl) {
                const flyerLabel = selectedFlyerTitle || 'Flayer';
                finalContent = `${finalContent}\n\n${flyerLabel}: ${selectedFlyerUrl}`.trim();
            }

            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(finalContent)}`;
            window.open(whatsappUrl, '_blank');
            saveRecentNumber(number);
            showNotification('Abriendo WhatsApp...', 'success');
        }

        // Guardar número reciente en localStorage
        function saveRecentNumber(number) {
            let recentNumbers = JSON.parse(localStorage.getItem('whatsapp_recent_numbers') || '[]');

            // Remover si ya existe
            recentNumbers = recentNumbers.filter(n => n !== number);

            // Agregar al inicio
            recentNumbers.unshift(number);

            // Mantener solo los últimos 8
            recentNumbers = recentNumbers.slice(0, 8);

            localStorage.setItem('whatsapp_recent_numbers', JSON.stringify(recentNumbers));

            // Actualizar vista
            loadRecentNumbers();
        }

        // Cargar números recientes
        function loadRecentNumbers() {
            const recentNumbers = JSON.parse(localStorage.getItem('whatsapp_recent_numbers') || '[]');
            const container = document.getElementById('recent_numbers');

            if (recentNumbers.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 font-medium">No hay números recientes</p>';
                return;
            }

            const numbersHtml = recentNumbers.map(number => `
                <button onclick="useRecentNumber('${number}')" 
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-purple-100 to-pink-100 text-[#8704BF] hover:from-purple-200 hover:to-pink-200 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md border border-purple-200">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    +51${number}
                </button>
            `).join('');

            container.innerHTML = numbersHtml;
        }

        // Usar número reciente
        function useRecentNumber(number) {
            document.getElementById('whatsapp_number').value = number;
            document.getElementById('whatsapp_number').focus();
        }

        // Copiar para WhatsApp (función original actualizada)
        function copyToWhatsApp(id) {
            const message = predefinedMessages.find(m => m.id === id);
            if (message) {
                navigator.clipboard.writeText(message.content).then(() => {
                    showNotification('Mensaje copiado para WhatsApp', 'success');
                });
            }
        }

        // Cargar mensajes recientes
        async function loadRecentMessages() {
            try {
                const response = await fetch('{{ route("api.messages.recent") }}?limit=15');
                const messages = await response.json();
                renderRecentMessages(messages);
            } catch (error) {
                console.error('Error loading recent messages:', error);
                document.getElementById('recent-messages-container').innerHTML =
                    '<div class="text-center py-8 text-red-600">Error al cargar mensajes recientes</div>';
            }
        }

        // Renderizar mensajes recientes
        function renderRecentMessages(messages) {
            const container = document.getElementById('recent-messages-container');

            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V9a2 2 0 01-2 2h-2m0 0v2a2 2 0 002 2h2a2 2 0 002-2v-2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mensajes recientes</h3>
                        <p class="mt-1 text-sm text-gray-500">Crea tu primer mensaje para comenzar.</p>
                    </div>
                `;
                return;
            }

            const messagesHtml = messages.map(message => `
                <div class="border-2 border-purple-200 rounded-xl p-6 hover:bg-white transition-all duration-300 transform hover:scale-105 bg-purple-50/50 shadow-sm hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-sm">
                                    #${message.message_number}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ${message.status_badge} shadow-sm">
                                    ${message.status.charAt(0).toUpperCase() + message.status.slice(1)}
                                </span>
                                <span class="text-xs text-purple-600 font-medium">${message.created_at_human}</span>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 truncate mb-2">${message.subject}</h4>
                            <p class="mt-1 text-sm text-gray-600 line-clamp-2 leading-relaxed">${message.content}</p>
                            <div class="mt-3 flex items-center text-xs text-purple-600 font-medium">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2m2 4h6m-6 4h6m-3-8V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4"></path>
                                </svg>
                                ${message.recipients_count} destinatarios
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="/messages/${message.id}" 
                               class="text-indigo-600 hover:text-indigo-900 p-2 rounded-full hover:bg-indigo-50 transition-all duration-200 shadow-sm hover:shadow-md"
                               title="Ver mensaje">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <button onclick="hideRecentMessage(${message.id})" 
                                    class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-all duration-200 shadow-sm hover:shadow-md"
                                    title="Ocultar mensaje">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = `<div class="space-y-3">${messagesHtml}</div>`;
        }

        // Ocultar mensaje reciente
        async function hideRecentMessage(messageId) {
            if (!confirm('¿Ocultar este mensaje? Se mantendrá guardado para exportación PDF.')) {
                return;
            }

            try {
                const response = await fetch(`/messages/${messageId}/hide`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Mensaje ocultado exitosamente', 'success');
                    loadRecentMessages(); // Recargar la lista
                } else {
                    showNotification('Error al ocultar el mensaje', 'error');
                }
            } catch (error) {
                console.error('Error hiding message:', error);
                showNotification('Error al ocultar el mensaje', 'error');
            }
        }

        // Actualizar mensajes recientes
        function refreshRecentMessages() {
            document.getElementById('recent-messages-container').innerHTML = `
                <div class="text-center py-8">
                    <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Actualizando...</p>
                </div>
            `;
            loadRecentMessages();
        }

        // Mostrar notificación
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('predefinedModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closePredefinedModal();
            }
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            line-clamp: 3;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Animación para la vista previa */
        #message_preview {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: top;
        }

        #message_preview.hidden {
            opacity: 0;
            transform: scaleY(0);
            max-height: 0;
            overflow: hidden;
        }

        #message_preview:not(.hidden) {
            opacity: 1;
            transform: scaleY(1);
            max-height: 500px;
        }

        /* Efectos hover mejorados */
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Gradientes animados */
        .gradient-animate {
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Estilo para números recientes mejorado */
        #recent_numbers button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #recent_numbers button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
        }

        /* Estilo para el campo de número mejorado */
        #whatsapp_number:focus {
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
            border-color: #22c55e;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        /* Animaciones de carga mejoradas */
        .animate-pulse-green {
            animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .7;
                transform: scale(1.05);
            }
        }

        /* Efectos de botones mejorados */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.4);
        }

        /* Mejoras en las tarjetas */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Efectos de brillo en encabezados */
        .header-shine {
            position: relative;
            overflow: hidden;
        }

        .header-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }
    </style>
    <!-- Save Template Modal -->
    <div id="saveTemplateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                onclick="closeSaveTemplateModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('settings.custom-messages.store') }}" method="POST">
                    @csrf
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold leading-6 text-[#5F1BF2]" id="modal-title">Guardar como Plantilla
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="template_name" class="block text-sm font-bold text-gray-700">Nombre de la
                                    Plantilla</label>
                                <input type="text" name="name" id="template_name" required
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#8704BF] focus:border-[#8704BF]"
                                    placeholder="Ej: Bienvenida Cliente">
                            </div>

                            <input type="hidden" name="content" id="template_content">

                            <div class="bg-purple-50 p-3 rounded-md border border-purple-100">
                                <label class="block text-xs font-bold text-[#5F1BF2] mb-1">Contenido a guardar:</label>
                                <p id="template_content_preview" class="text-sm text-gray-600 italic line-clamp-3"></p>
                            </div>

                            <input type="hidden" name="type" value="whatsapp">
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-[#5F1BF2] border border-transparent rounded-md shadow-sm hover:bg-[#4c15c2] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm shadow-lg shadow-[#5F1BF2]/30">
                            Guardar Plantilla
                        </button>
                        <button type="button" onclick="closeSaveTemplateModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
