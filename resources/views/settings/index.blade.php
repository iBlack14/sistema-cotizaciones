<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight drop-shadow-md">
            {{ __('Ajustes del Sistema') }}
        </h2>
    </x-slot>

    <!-- Emoji Picker Library -->
    <script type="module">
        import { Picker } from 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';
        
        window.insertEmoji = function(textareaId, event) {
            const picker = new Picker();
            const existingPicker = document.querySelector('emoji-picker');
            if (existingPicker) existingPicker.remove();
            
            picker.addEventListener('emoji-click', e => {
                const textarea = document.getElementById(textareaId);
                const emoji = e.detail.unicode;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                textarea.value = text.substring(0, start) + emoji + text.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
                textarea.focus();
                
                // Trigger input event for Alpine.js reactivity
                textarea.dispatchEvent(new Event('input'));
                
                picker.remove();
            });
            
            picker.style.position = 'absolute';
            picker.style.zIndex = '9999';
            const button = event.target.closest('button');
            const rect = button.getBoundingClientRect();
            picker.style.top = (rect.bottom + window.scrollY) + 'px';
            picker.style.left = rect.left + 'px';
            document.body.appendChild(picker);
            
            // Close picker when clicking outside
            setTimeout(() => {
                document.addEventListener('click', function closePickerHandler(e) {
                    if (!picker.contains(e.target) && e.target !== button) {
                        picker.remove();
                        document.removeEventListener('click', closePickerHandler);
                    }
                }, {once: false});
            }, 100);
        };
    </script>

    <div class="py-12" x-data="{ activeTab: 'messages', editingMessage: null, editForm: { name: '', content: '', type: 'both', usage: 'general' }, showNewMessageForm: false, newMessageUsage: 'general' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
            
            <!-- Sidebar Navigation -->
            <div class="w-full md:w-64 flex-shrink-0">
                <div class="bg-white/30 backdrop-blur-xl rounded-2xl p-3 shadow-lg border border-white/50 flex flex-col gap-2 sticky top-6">
                    <button @click="activeTab = 'messages'"
                            :class="activeTab === 'messages' ? 'bg-gradient-to-r from-vc-purple to-vc-magenta text-white shadow-md' : 'text-gray-800 hover:bg-white/50'"
                            class="w-full rounded-xl py-3 px-4 text-sm font-bold flex items-center gap-3 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Mensajes y Plantillas
                    </button>
                    
                    <button @click="activeTab = 'images'"
                            :class="activeTab === 'images' ? 'bg-gradient-to-r from-vc-purple to-vc-magenta text-white shadow-md' : 'text-gray-800 hover:bg-white/50'"
                            class="w-full rounded-xl py-3 px-4 text-sm font-bold flex items-center gap-3 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Imágenes y Anexos
                    </button>
                </div>
            </div>

            <div class="flex-1">
                <div class="bg-white/80 backdrop-blur-2xl overflow-hidden shadow-2xl sm:rounded-3xl border border-white/60">
                    <div class="p-8 md:p-10">
                        @if (session('success'))
                            <div class="mb-6 bg-green-50/80 backdrop-blur-sm border-l-4 border-green-500 text-green-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="block font-medium">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-6 bg-red-50/80 backdrop-blur-sm border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span class="block font-medium">{{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- ================================ TAB: MESSAGES ================================ -->
                        <div x-show="activeTab === 'messages'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-8 border-b border-gray-200/60 pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <h3 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-vc-purple to-vc-magenta tracking-tight">Plantillas del Sistema</h3>
                                        <p class="text-sm text-gray-500 mt-1 font-medium">Personaliza los mensajes automáticos para Cotizaciones y Seguimientos.</p>
                                    </div>
                                    <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 rounded-xl font-bold text-white shadow-lg lg:shadow-vc-magenta/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 bg-gradient-to-r from-vc-purple to-vc-magenta">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                        Guardar Todas
                                    </button>
                                </div>

                                <!-- Variables Cheat Sheet (Compact Tags) -->
                                <div class="mb-8 flex flex-wrap items-center gap-2 bg-purple-50/50 p-4 rounded-2xl border border-purple-100/50">
                                    <span class="text-xs font-bold text-vc-purple uppercase tracking-wider mr-2">Variables:</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Nombre del Cliente">[Nombre]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Empresa Cliente">[Empresa]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="RUC del Cliente">[RUC]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Fecha de Cotización">[Fecha]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Servicio Principal">[Servicio]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Monto Total Oferta">[Total]</span>
                                    <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Enlace Público PDF">[Link]</span>
                                </div>

                                <div class="space-y-6">
                                    @php
                                        $msgBlocks = [
                                            ['id' => 'quotation', 'title' => '1. Envío Inicial de Cotización', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                            ['id' => 'confirmation', 'title' => '2. Seguimiento: Confirmación', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                                            ['id' => 'service', 'title' => '3. Coordinación de Servicio', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                            ['id' => 'access', 'title' => '4. Solicitud de Accesos', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                                            ['id' => 'resend', 'title' => '5. Reenvío de Cotización', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15']
                                        ];
                                    @endphp

                                    @foreach($msgBlocks as $block)
                                    <div class="bg-white/60 p-6 rounded-2xl shadow-sm border border-gray-100/80 hover:border-vc-purple/30 transition-all duration-300 group">
                                        <div class="flex items-center gap-3 mb-5">
                                            <div class="p-2.5 bg-purple-50 rounded-xl text-vc-purple group-hover:bg-vc-purple group-hover:text-white transition-colors duration-300">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $block['icon'] }}"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-800">{{ $block['title'] }}</h3>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                                            <!-- Email Column -->
                                            <div class="relative">
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="flex items-center text-sm font-bold text-gray-700 bg-blue-50/50 px-3 py-1 rounded-md border border-blue-100">
                                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                        Correo (Email)
                                                    </label>
                                                    <button type="button" onclick="insertEmoji('{{ $block['id'] }}_email_message', event)" class="text-gray-400 hover:text-vc-magenta transition-colors p-1" title="Insertar emoji">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    </button>
                                                </div>
                                                <textarea id="{{ $block['id'] }}_email_message" name="{{ $block['id'] }}_email_message" rows="4" class="w-full rounded-xl border-gray-200 shadow-inner bg-gray-50/30 focus:bg-white focus:border-vc-magenta focus:ring-vc-magenta text-sm text-gray-900 transition-all placeholder-gray-400">{{ $settings[$block['id'].'_email_message'] ?? '' }}</textarea>
                                            </div>
                                            <!-- WhatsApp Column -->
                                            <div class="relative">
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="flex items-center text-sm font-bold text-gray-700 bg-green-50/50 px-3 py-1 rounded-md border border-green-100">
                                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.031 2.008c-5.467 0-9.919 4.452-9.919 9.919 0 1.748.457 3.456 1.325 4.962L2.016 21.99l5.241-1.374a9.882 9.882 0 004.774 1.229c5.466 0 9.918-4.452 9.918-9.919 0-5.466-4.452-9.918-9.918-9.918zm4.459 14.305c-.195.55-1.127 1.05-1.547 1.1-.42.05-1.042.138-3.321-.806-2.738-1.134-4.512-3.921-4.65-4.104-.138-.182-1.11-1.48-1.11-2.82s.696-1.996.945-2.263c.249-.267.542-.334.724-.334.183 0 .367 0 .52.008.163.008.384-.062.597.452.214.514.733 1.791.801 1.928.068.138.113.298.023.48-.09.183-.136.298-.27.447-.137.15-.286.33-.404.454-.128.134-.265.281-.11.548.156.267.697 1.15 1.493 1.861.942.84 1.797 1.112 2.072 1.246.275.134.437.114.6-.07.163-.184.697-.817.88-1.098.183-.281.368-.234.622-.143.253.09 1.603.755 1.878.892.275.138.455.206.52.32m0 0" clip-rule="evenodd" /></svg>
                                                        WhatsApp
                                                    </label>
                                                    <button type="button" onclick="insertEmoji('{{ $block['id'] }}_whatsapp_message', event)" class="text-gray-400 hover:text-vc-magenta transition-colors p-1" title="Insertar emoji">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    </button>
                                                </div>
                                                <textarea id="{{ $block['id'] }}_whatsapp_message" name="{{ $block['id'] }}_whatsapp_message" rows="4" class="w-full rounded-xl border-gray-200 shadow-inner bg-gray-50/30 focus:bg-white focus:border-vc-magenta focus:ring-vc-magenta text-sm text-gray-900 transition-all placeholder-gray-400">{{ $settings[$block['id'].'_whatsapp_message'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </form>
                            
                            <hr class="my-10 border-gray-100">

                            <div class="mb-8 border-b border-gray-200/60 pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-vc-purple to-vc-magenta tracking-tight">Panel de Dominios: WhatsApp</h3>
                                    <p class="text-sm text-gray-500 mt-1 font-medium">Define los mensajes rápidos para el dashboard de dominios.</p>
                                </div>
                            </div>

                            <!-- Variables Dashboard Cheat Sheet -->
                             <div class="mb-8 flex flex-wrap items-center gap-2 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50">
                                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider mr-2">Variables Dashboard:</span>
                                <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Nombre del Cliente">[Nombre]</span>
                                <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Nombre del Dominio">[Dominio]</span>
                                <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Precio del Servicio">[Precio]</span>
                                <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Días para Vencer">[Dias]</span>
                                <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-xs font-mono font-medium shadow-sm text-gray-700 hover:border-vc-magenta cursor-help" title="Fecha Vencimiento">[Fecha_Vencimiento]</span>
                            </div>

                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="space-y-6">
                                    @php
                                        $dashBlocks = [
                                            ['id' => 'wa_activation', 'title' => 'Confirmación de Activación', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            ['id' => 'wa_expiry', 'title' => 'Recordatorio de Vencimiento', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            ['id' => 'wa_promo', 'title' => 'Recordatorio de Pago / Saldo', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
                                        ];
                                    @endphp

                                    @foreach($dashBlocks as $block)
                                    <div class="bg-white/60 p-6 rounded-2xl shadow-sm border border-gray-100/80 hover:border-vc-purple/30 transition-all duration-300 group">
                                        <div class="flex items-center justify-between gap-3 mb-5">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $block['icon'] }}"></path></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-800">{{ $block['title'] }}</h3>
                                            </div>
                                            <button type="button" onclick="insertEmoji('{{ $block['id'] }}', event)" class="text-gray-400 hover:text-vc-magenta transition-colors p-1" title="Insertar emoji">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                        </div>
                                        
                                        <textarea id="{{ $block['id'] }}" name="{{ $block['id'] }}" rows="3" class="w-full rounded-xl border-gray-200 shadow-inner bg-gray-50/30 focus:bg-white focus:border-vc-magenta focus:ring-vc-magenta text-sm text-gray-900 transition-all placeholder-gray-400">{{ $settings[$block['id']] ?? '' }}</textarea>
                                    </div>
                                    @endforeach
                                    
                                    <div class="flex justify-end pt-4">
                                        <button type="submit" class="inline-flex justify-center items-center px-10 py-3 rounded-xl font-black text-white shadow-lg lg:shadow-vc-magenta/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 bg-gradient-to-r from-vc-purple to-[#F2059F]">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                            Actualizar Mensajes Dashboard
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Custom Dashboard Messages -->
                            <div class="mt-12 bg-white/40 p-8 rounded-3xl border border-gray-100 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                                    <div>
                                        <h4 class="text-xl font-black text-gray-800">Mensajes Adicionales Dashboard</h4>
                                        <p class="text-sm text-gray-500 mt-1 font-medium">Plantillas creadas específicamente para la gestión de dominios.</p>
                                    </div>
                                    <button @click="showNewMessageForm = !showNewMessageForm; newMessageUsage = 'dashboard'; document.getElementById('custom_messages_section').scrollIntoView({behavior: 'smooth'})" 
                                            class="px-5 py-2.5 bg-vc-magenta/10 text-vc-magenta font-black rounded-xl hover:bg-vc-magenta hover:text-white transition-all flex items-center gap-2 border border-vc-magenta/20 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Nuevo Mensaje Dashboard
                                    </button>
                                </div>

                                @if($msgByUsage['dashboard']->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                    @foreach($msgByUsage['dashboard'] as $message)
                                        @include('settings.partials.message_card', ['message' => $message])
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                    <p class="text-gray-400 font-medium italic">No hay mensajes personalizados para el dashboard.</p>
                                </div>
                                @endif
                            </div>

                            <!-- Custom Messages Section -->
                            <div class="mt-16 mb-8 border-t-2 border-gray-100 pt-10">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                                    <div>
                                        <h3 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                                            <svg class="w-6 h-6 text-vc-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Mensajes Guardados
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">Crea bloques de texto reutilizables para el chat/correo.</p>
                                    </div>
                                    <button @click="showNewMessageForm = !showNewMessageForm" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2">
                                        <span x-show="!showNewMessageForm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Nuevo Mensaje</span>
                                        <span x-show="showNewMessageForm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Cancelar</span>
                                    </button>
                                </div>

                                <!-- Create New Message Form -->
                                <div id="custom_messages_section" x-show="showNewMessageForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-gradient-to-br from-purple-50/80 to-pink-50/80 p-6 md:p-8 rounded-2xl shadow-sm border border-purple-200/60 mb-8" style="display: none;">
                                    <h4 class="text-lg font-bold text-vc-purple mb-5 flex items-center gap-2">✨ Redactar Nuevo Mensaje</h4>
                                    <form action="{{ route('settings.custom-messages.store') }}" method="POST">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-5">
                                            <div class="md:col-span-6">
                                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nombre / Título</label>
                                                <input type="text" name="name" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-vc-magenta focus:ring-vc-magenta text-sm bg-white text-gray-900" placeholder="Ej: Promoción Fiestas Patrias..." required>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Canal (Tipo)</label>
                                                <select name="type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-vc-magenta focus:ring-vc-magenta text-sm bg-white font-medium text-gray-700" required>
                                                    <option value="both">Email y WhatsApp</option>
                                                    <option value="email">Solo Email</option>
                                                    <option value="whatsapp">Solo WhatsApp</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Uso previsto</label>
                                                <select name="usage" x-model="newMessageUsage" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-vc-magenta focus:ring-vc-magenta text-sm bg-white font-medium text-gray-700" required>
                                                    <option value="general">Uso General</option>
                                                    <option value="quotation">Cotizaciones</option>
                                                    <option value="dashboard">Dashboard Dominios</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Cuerpo del Mensaje</label>
                                                <button type="button" onclick="insertEmoji('new_custom_message_content', event)" class="text-gray-400 hover:text-vc-magenta transition-colors p-1" title="Insertar emoji">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </button>
                                            </div>
                                            <textarea id="new_custom_message_content" name="content" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-vc-magenta focus:ring-vc-magenta text-sm leading-relaxed text-gray-900" placeholder="¡Hola [Nombre]! Tenemos una nueva promoción para ti..." required></textarea>
                                        </div>
                                        <div class="mt-6 flex justify-end">
                                            <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold shadow-md hover:bg-black hover:shadow-lg transition-all">
                                                Guardar Mensaje
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Saved Custom Messages Grid (Categorized) -->
                                <div class="space-y-12">
                                    @php
                                        $displayCategories = [
                                            ['key' => 'quotation', 'title' => 'Mensajes de Cotización', 'desc' => 'Plantillas enviadas junto a cotizaciones o seguimientos.'],
                                            ['key' => 'general', 'title' => 'Mensajes Generales', 'desc' => 'Respuestas rápidas y plantillas de uso variado.']
                                        ];
                                    @endphp

                                    @foreach($displayCategories as $cat)
                                        @if($msgByUsage[$cat['key']]->count() > 0)
                                        <div>
                                            <div class="mb-4">
                                                <h5 class="text-lg font-black text-gray-800">{{ $cat['title'] }}</h5>
                                                <p class="text-xs text-gray-400">{{ $cat['desc'] }}</p>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                                @foreach($msgByUsage[$cat['key']] as $message)
                                                    @include('settings.partials.message_card', ['message' => $message])
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    
                                    @if($msgByUsage['quotation']->count() === 0 && $msgByUsage['general']->count() === 0)
                                    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300 shadow-sm">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-400">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-700">No hay mensajes guardados</h3>
                                        <p class="text-gray-500 text-sm mt-1 max-w-sm mx-auto">Haz clic en "Nuevo Mensaje" para guardar plantillas de respuesta frecuentes.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ================================ TAB: IMAGES ================================ -->
                        <div x-show="activeTab === 'images'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            
                            <div class="mb-8 border-b border-gray-200/60 pb-6">
                                <h3 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-vc-purple to-vc-magenta tracking-tight">Imágenes Anexas a PDF</h3>
                                <p class="text-sm text-gray-500 mt-1 font-medium">Vincula imágenes al dorso de las cotizaciones según el servicio.</p>
                            </div>

                            <!-- Upload Section -->
                            <div class="mb-10 bg-gradient-to-br from-indigo-50/50 to-purple-50/50 p-6 md:p-8 rounded-3xl border border-indigo-100 shadow-sm">
                                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Subir Nueva Imagen
                                </h4>
                                 <form action="{{ route('settings.images.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                     @csrf
                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                         <div>
                                             <label for="service_image_name" class="block text-xs font-bold text-gray-600 mb-2">Nombre Descriptivo</label>
                                             <input id="service_image_name" type="text" name="name" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5" placeholder="Ej: Portafolio Diseño Web...">
                                         </div>
                                         <div>
                                             <label class="block text-xs font-bold text-gray-600 mb-2">Archivos (Puedes seleccionar varios)</label>
                                             <input type="file" name="images[]" class="w-full text-sm text-gray-600 bg-white border border-gray-300 rounded-xl shadow-sm file:mr-4 file:py-2.5 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors cursor-pointer" accept="image/*" multiple required>
                                         </div>
                                     </div>

                                     <div class="bg-white/50 p-4 rounded-2xl border border-white flex flex-col md:flex-row gap-4 items-end">
                                         <div class="w-full md:flex-1">
                                             <label class="block text-xs font-bold text-vc-purple uppercase tracking-widest mb-1.5 opacity-90 italic">Opcional: Asignar Automáticamente a...</label>
                                             <select id="auto_service_name_select" name="auto_service_name" class="w-full text-sm rounded-xl border-gray-200 bg-white text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                 <option value="">(No asignar aún)</option>
                                                 @foreach($services as $service)
                                                     <option value="{{ $service }}">{{ $service }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                         <div id="auto_service_name_custom_wrapper" class="w-full md:flex-1 hidden">
                                             <label for="auto_service_name_custom" class="block text-xs font-bold text-gray-600 mb-1.5">Nombre Nuevo Servicio</label>
                                             <input id="auto_service_name_custom" type="text" name="auto_service_name_custom" placeholder="Escribe el nombre del servicio..." class="w-full text-sm rounded-xl border-gray-200 bg-white text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                         </div>
                                         <button type="submit" class="w-full md:w-auto px-10 py-2.5 bg-gradient-to-r from-indigo-600 to-[#8704BF] text-white rounded-xl font-black shadow-lg hover:shadow-indigo-300/30 transition-all">
                                             Subir y Guardar
                                         </button>
                                     </div>
                                 </form>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
                                <!-- Image Gallery -->
                                <div class="xl:col-span-7">
                                    <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-5">Galería Disponible ({{ $images->count() }})</h4>
                                    
                                    @if($images->count() > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                                        @foreach($images as $image)
                                            <div class="group relative bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                                                <div class="aspect-[4/3] bg-gray-50 flex items-center justify-center p-2 relative overflow-hidden">
                                                    <!-- Delete Overlay -->
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity z-10 flex items-center justify-center backdrop-blur-sm">
                                                        <form action="{{ route('settings.images.delete', $image) }}" method="POST" onsubmit="return confirm('¿Eliminar esta imagen del servidor?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-bold hover:bg-red-600 hover:scale-105 transition-all shadow-lg flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                Borrar
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <img src="{{ $image->url }}" alt="{{ $image->name }}" data-fallback="{{ asset('images/logo.png') }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null; this.src=this.dataset.fallback;">
                                                </div>
                                                <div class="p-3 border-t border-gray-50 bg-white relative z-20 tooltip" title="{{ $image->name }}">
                                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $image->name }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-0.5">ID: {{ $image->id }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-16 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                        <div class="inline-flex w-16 h-16 bg-white rounded-full items-center justify-center shadow-sm text-gray-300 mb-4"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                                        <p class="text-gray-500 font-medium">Sube tu primera imagen para anexar.</p>
                                    </div>
                                    @endif
                                </div>

                                <!-- Service Mappings -->
                                <div class="xl:col-span-5 flex flex-col gap-6">
                                    <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Reglas de Asignación</h4>
                                    
                                    <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 overflow-hidden flex flex-col h-full">
                                        <!-- Add New Mapping Form -->
                                        <div class="p-6 bg-gradient-to-br from-indigo-600 to-[#8704BF] border-b border-white/10 text-white relative overflow-hidden">
                                            <!-- Decorator -->
                                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                                            <div class="absolute right-12 bottom-0 w-32 h-32 bg-fuchsia-500/20 rounded-full blur-2xl"></div>

                                            <h5 class="text-white font-bold mb-4 relative z-10">Nueva Regla Automática</h5>
                                            <form action="{{ route('settings.mappings.update') }}" method="POST" class="flex flex-col gap-4 relative z-10">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-bold text-indigo-100 uppercase tracking-widest mb-1.5 opacity-90">1. Cuando el Servicio sea...</label>
                                                    <select id="service_name_select" name="service_name" class="w-full text-sm rounded-xl border-0 bg-white/20 text-white placeholder-white/50 focus:ring-2 focus:ring-white transition-all [&>option]:text-gray-900" required>
                                                        <option value="">Selecciona servicio...</option>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service }}" {{ old('service_name') === $service ? 'selected' : '' }}>{{ $service }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div id="service_name_custom_wrapper" class="{{ old('service_name') === 'OTRO' ? '' : 'hidden' }}">
                                                    <label for="service_name_custom" class="block text-xs font-bold text-indigo-100 uppercase tracking-widest mb-1.5 opacity-90">Nombre Específico (Otro)</label>
                                                    <input id="service_name_custom" type="text" name="service_name_custom" value="{{ old('service_name_custom') }}" placeholder="Escribe el nombre exacto..." class="w-full text-sm rounded-xl border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-indigo-100 uppercase tracking-widest mb-1.5 opacity-90">2. Anexar la Imagen...</label>
                                                    <select name="service_image_id" class="w-full text-sm rounded-xl border-0 bg-white/20 text-white placeholder-white/50 focus:ring-2 focus:ring-white transition-all [&>option]:text-gray-900" required>
                                                        <option value="">Selecciona imagen...</option>
                                                        @foreach($images as $image)
                                                            <option value="{{ $image->id }}">{{ $image->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" class="w-full px-4 py-3 mt-1 bg-white text-indigo-600 text-sm font-black rounded-xl hover:bg-gray-50 hover:shadow-lg transition-all">
                                                    Crear Regla
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Mappings List -->
                                        <div class="flex-1 max-h-[400px] overflow-y-auto bg-gray-50/30 p-2">
                                            @if($mappings->count() > 0)
                                                @php
                                                    $groupedMappings = $mappings->groupBy('service_name');
                                                @endphp
                                                <div class="space-y-4">
                                                    @foreach($groupedMappings as $serviceName => $serviceMappings)
                                                    <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden">
                                                        <div class="bg-gray-50 px-3 py-2 border-b border-gray-100 flex justify-between items-center">
                                                            <h6 class="text-xs font-black text-vc-purple uppercase tracking-tight">{{ $serviceName }}</h6>
                                                            <span class="bg-vc-purple/10 text-vc-purple px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $serviceMappings->count() }} img</span>
                                                        </div>
                                                        <div class="p-2 space-y-2">
                                                            @foreach($serviceMappings as $mapping)
                                                            <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="h-10 w-12 rounded-lg overflow-hidden border border-gray-100 bg-fuchsia-50/30 flex items-center justify-center">
                                                                        @if($mapping->serviceImage)
                                                                            <img src="{{ $mapping->serviceImage->url }}" class="h-full w-full object-contain" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                                                                        @else
                                                                            <span class="text-[9px] text-gray-300">N/A</span>
                                                                        @endif
                                                                    </div>
                                                                    <span class="text-[11px] font-bold text-gray-700 group-hover:text-vc-magenta transition-colors">{{ $mapping->serviceImage ? $mapping->serviceImage->name : 'Imagen Rota' }}</span>
                                                                </div>
                                                                <form action="{{ route('settings.mappings.delete', $mapping) }}" method="POST" onsubmit="return confirm('¿Quitar esta imagen del servicio?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors p-1" title="Eliminar regla">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-gray-400 py-12">
                                                    <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                                    <p class="text-sm font-medium">Aún no hay reglas vinculadas.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Help text compact -->
                                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-start gap-3">
                                        <div class="bg-blue-100 text-blue-600 p-1.5 rounded-lg shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                            Al vincular una imagen con un servicio, ésta se adjuntará automáticamente al generar los <span class="font-bold underline decoration-blue-300">PDFs de cotización</span> para brindar mayor impacto visual al cliente.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('service_name_select');
    const wrapper = document.getElementById('service_name_custom_wrapper');
    const customInput = document.getElementById('service_name_custom');
    
    // Auto assignment fields in upload form
    const autoSelect = document.getElementById('auto_service_name_select');
    const autoWrapper = document.getElementById('auto_service_name_custom_wrapper');
    const autoCustomInput = document.getElementById('auto_service_name_custom');

    const toggleCustomField = function (selectEl, wrapperEl, inputEl) {
        if (!selectEl || !wrapperEl || !inputEl) return;
        const isOther = (selectEl.value || '').trim().toUpperCase() === 'OTRO';
        wrapperEl.classList.toggle('hidden', !isOther);
        inputEl.required = isOther;
    };

    if (select) {
        select.addEventListener('change', () => toggleCustomField(select, wrapper, customInput));
        toggleCustomField(select, wrapper, customInput);
    }

    if (autoSelect) {
        autoSelect.addEventListener('change', () => toggleCustomField(autoSelect, autoWrapper, autoCustomInput));
        toggleCustomField(autoSelect, autoWrapper, autoCustomInput);
    }
});
</script>
<style>
/* Utilities for custom scrollbar in mappings list */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 10px;
}
</style>
</x-app-layout>
