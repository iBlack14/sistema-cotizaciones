<x-app-layout>
    <!-- Emoji Picker Library -->
    <script type="module">
        import { Picker } from 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

        window.insertEmoji = function(textareaId) {
            const picker = new Picker();
            const textarea = document.getElementById(textareaId);
            
            if (!textarea) {
                console.error('Textarea not found:', textareaId);
                return;
            }

            // Position picker near the button
            const button = event.target.closest('button');
            const rect = button.getBoundingClientRect();
            picker.style.position = 'absolute';
            picker.style.zIndex = '9999';
            picker.style.top = (rect.bottom + window.scrollY) + 'px';
            picker.style.left = (rect.left + window.scrollX - 320) + 'px';

            // Handle emoji selection
            picker.addEventListener('emoji-click', (event) => {
                const emoji = event.detail.unicode;
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

            // Close picker when clicking outside
            const closeHandler = (e) => {
                if (!picker.contains(e.target) && e.target !== button) {
                    picker.remove();
                    document.removeEventListener('click', closeHandler);
                }
            };
            setTimeout(() => document.addEventListener('click', closeHandler), 100);

            document.body.appendChild(picker);
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Alpine && Alpine.store('whatsapp')) {
                const activation = @json($settings['wa_activation'] ?? "");
                const expiry = @json($settings['wa_expiry'] ?? "");
                const promo = @json($settings['wa_promo'] ?? "");
                
                const templates = Alpine.store('whatsapp').templates;
                if (activation) templates.activation = activation;
                if (expiry) templates.expiry = expiry;
                if (promo) templates.promo = promo;

                // Add custom messages to Alpine store
                const customMessages = @json($customMessages->pluck('content', 'id'));
                Object.keys(customMessages).forEach(id => {
                    templates[`custom_${id}`] = customMessages[id];
                });

                // Update sendDirect to handle more placeholders
                const originalSendDirect = Alpine.store('whatsapp').sendDirect;
                Alpine.store('whatsapp').sendDirect = function(phone, domainName, clientName, templateKey) {
                    if (!phone) { alert('Este dominio no tiene un número de teléfono registrado.'); return; }
                    let message = this.templates[templateKey] || '';
                    
                    // Replace ALL types of placeholders
                    message = message.replace(/\[nombre\]/gi, clientName || 'Cliente');
                    message = message.replace(/\[dominio\]/gi, domainName || '');
                    message = message.replace(/\{cliente\}/gi, clientName || 'Cliente');
                    message = message.replace(/\{dominio\}/gi, domainName || '');
                    
                    const encodedMessage = encodeURIComponent(message);
                    const cleanPhone = phone.replace(/\D/g, '');
                    window.open(`https://wa.me/${cleanPhone}?text=${encodedMessage}`, '_blank');
                };
            }
        });
    </script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Panel de Dominios') }}
        </h2>
    </x-slot>

    <div class="py-12 pb-32">
        <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-12 relative">
            <div class="space-y-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Dominios -->
                <div class="bg-[#5F1BF2] rounded-2xl p-6 text-white shadow-xl shadow-[#5F1BF2]/20 backdrop-blur transform hover:scale-105 transition-transform duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white/80 text-sm font-medium uppercase tracking-wide">Total Dominios</p>
                            <h3 class="text-4xl font-bold mt-2">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Activos -->
                <div class="bg-emerald-600 rounded-2xl p-6 text-white shadow-xl shadow-emerald-500/20 backdrop-blur transform hover:scale-105 transition-transform duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white/80 text-sm font-medium uppercase tracking-wide">Activos</p>
                            <h3 class="text-4xl font-bold mt-2">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Por Vencer -->
                <div class="bg-amber-600 rounded-2xl p-6 text-white shadow-xl shadow-amber-500/20 backdrop-blur transform hover:scale-105 transition-transform duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white/80 text-sm font-medium uppercase tracking-wide">Por Vencer</p>
                            <h3 class="text-4xl font-bold mt-2">{{ $stats['expiring'] }}</h3>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Expirados -->
                <div class="bg-red-600 rounded-2xl p-6 text-white shadow-xl shadow-red-500/20 backdrop-blur transform hover:scale-105 transition-transform duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white/80 text-sm font-medium uppercase tracking-wide">Expirados</p>
                            <h3 class="text-4xl font-bold mt-2">{{ $stats['expired'] }}</h3>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <!-- Search and Filters -->
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl p-6 border border-white/50">
                    <form method="GET" action="{{ route('domains.index') }}" class="space-y-4">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col xl:flex-row gap-4">
                                <!-- Search Bar -->
                                <div class="flex-1">
                                    <div class="relative flex items-center">
                                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Buscar por dominio, cliente..." class="block w-full pl-4 pr-12 py-4 border-2 border-[#8704BF] rounded-xl focus:ring-4 focus:ring-[#8704BF]/20 focus:border-[#8704BF] bg-white text-gray-900 placeholder-gray-500 shadow-sm text-lg font-medium">
                                        <div class="absolute right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-6 w-6 text-[#8704BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Buttons -->
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" name="status" value="" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ !request('status') ? 'bg-[#5F1BF2] text-white shadow-lg' : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-[#8704BF]' }}">
                                        Todos
                                    </button>
                                    <button type="submit" name="status" value="activo" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request('status') === 'activo' ? 'bg-[#5F1BF2] text-white shadow-lg' : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-[#8704BF]' }}">
                                        Activos
                                    </button>
                                    <button type="submit" name="status" value="por_vencer" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request('status') === 'por_vencer' ? 'bg-[#5F1BF2] text-white shadow-lg' : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-[#8704BF]' }}">
                                        Por Vencer
                                    </button>
                                    <button type="submit" name="status" value="expirado" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request('status') === 'expirado' ? 'bg-[#5F1BF2] text-white shadow-lg' : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-[#8704BF]' }}">
                                        Expirados
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button x-data="{}" @click="$dispatch('open-email-modal')" type="button" class="px-4 py-3 bg-[#5F1BF2] hover:bg-[#4c16c2] text-white rounded-xl font-semibold text-sm transition-all flex items-center gap-2 shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Enviar Correo
                                </button>

                                <button x-data="{}" @click="$dispatch('open-import-modal')" type="button" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-all flex items-center gap-2 shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Importar Excel
                                </button>

                                <a href="{{ route('domains.export-word', array_filter(['search' => request('search'), 'status' => request('status')])) }}" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm transition-all flex items-center gap-2 shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Exportar Word
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Domain Cards Grid Container -->
                <div class="relative min-w-0">
                    <!-- Loading Indicator -->
                    <div id="loading-indicator" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center z-10">
                        <div class="flex flex-col items-center gap-3">
                            <div class="relative">
                                <div class="w-16 h-16 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
                            </div>
                            <p class="text-sm font-semibold text-purple-600 animate-pulse">Buscando dominios...</p>
                        </div>
                    </div>

                    <div id="domains-grid">
                        @include('domains.partials.list')
                    </div>
                </div>
            </div>

            <aside class="hidden 2xl:block fixed top-32 right-10 w-80 z-30">
                @include('domains.partials.reminders-panel')
            </aside>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <a href="{{ route('domains.create') }}" class="fixed bottom-24 right-8 bg-[#8704BF] text-white p-6 rounded-full shadow-2xl shadow-[#8704BF]/50 hover:shadow-3xl hover:scale-110 focus:outline-none focus:ring-4 focus:ring-[#8704BF]/50 transition-all duration-300 z-50 group border-2 border-white/20">
        <svg class="w-10 h-10 transform group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </a>

    <!-- Import Modal -->
    <div x-data="{ fileName: '' }" 
         x-show="$store.importModal.show" 
         @keydown.escape.window="$store.importModal.close()"
         @open-import-modal.window="$store.importModal.open()"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="$store.importModal.close()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8 transform transition-all" @click.stop>
                <button @click="$store.importModal.close()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-emerald-600 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Importar Dominios</h3>
                            <p class="text-sm text-gray-500">Subir archivo Excel con dominios</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('domains.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Archivo Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required @change="fileName = $event.target.files[0]?.name || ''" class="block w-full text-sm text-gray-900 border-2 border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none focus:border-[#8704BF] file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-[#5F1BF2] file:text-white hover:file:opacity-90">
                        <p x-show="fileName" class="mt-2 text-sm text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span x-text="fileName"></span>
                        </p>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="$store.importModal.close()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition-colors">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold shadow-lg transition-all flex items-center gap-2">Importar Dominios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div x-data="{ 
            show: false, 
            selectedDomains: [], 
            selectAll: false,
            subject: '',
            message: '',
            currentTemplate: '',
            templateType: 'simple',
            templates: {
                expiry: {
                    subject: 'Aviso Importante: Su servicio está por vencer',
                    message: 'Estimado/a {cliente},\n\nLe informamos que su dominio {dominio} está próximo a vencer en {dias} días (fecha: {fecha_vencimiento}).\n\nPor favor, realice la renovación de S/ {precio} para evitar la suspensión del servicio.\n\nAtentamente,\nEl equipo.'
                },
                promo: {
                    subject: '¡Promoción Especial para {cliente}!',
                    message: 'Hola {cliente},\n\nQueremos ofrecerle una promoción especial para la renovación de su dominio {dominio}.\n\nPrecio especial: S/ {precio}\n\n¡Aproveche ahora!\n\nSaludos.'
                },
                activation: {
                    subject: 'Bienvenido: Su servicio {dominio} ha sido activado',
                    message: 'Estimado/a {cliente},\n\nSu dominio {dominio} y servicios han sido activados exitosamente.\n\nYa puede disfrutar de todos los beneficios.\n\nGracias por su preferencia.'
                }
            },
            applyTemplate() {
                if (this.currentTemplate && this.templates[this.currentTemplate]) {
                    this.subject = this.templates[this.currentTemplate].subject;
                    this.message = this.templates[this.currentTemplate].message;
                }
            },
            toggleAll() {
                this.selectAll = !this.selectAll;
                if (this.selectAll) {
                    this.selectedDomains = {{ $allDomains->pluck('id') }};
                } else {
                    this.selectedDomains = [];
                }
            }
         }" 
         x-show="show" 
         @keydown.escape.window="show = false"
         @open-email-modal.window="show = true"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="show = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-8 transform transition-all" @click.stop>
                <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-[#5F1BF2] p-3 rounded-xl">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Enviar Correo Masivo</h3>
                            <p class="text-sm text-gray-500">Envía correos a los dominios seleccionados</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('emails.send') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Plantilla Rápida</label>
                            <select x-model="currentTemplate" @change="applyTemplate()" class="block w-full border-2 border-gray-100 rounded-xl focus:border-[#8704BF] text-gray-900 font-medium">
                                <option value="">Seleccionar una plantilla...</option>
                                <option value="expiry">📅 Servicio por Vencer</option>
                                <option value="promo">✨ Promociones Especiales</option>
                                <option value="activation">🚀 Cuenta Activada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Asunto</label>
                            <input type="text" name="subject" x-model="subject" required class="block w-full border-2 border-gray-100 rounded-xl focus:border-[#8704BF] text-gray-900 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mensaje</label>
                            <textarea name="message" x-model="message" rows="6" required class="block w-full border-2 border-gray-100 rounded-xl focus:border-[#8704BF] text-gray-900 font-medium"></textarea>
                        </div>
                    </div>
                    <div class="flex flex-col h-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dominios Seleccionados (<span x-text="selectedDomains.length"></span>)</label>
                        <div class="flex-1 overflow-y-auto max-h-[300px] border-2 border-gray-200 rounded-xl p-4 bg-gray-50 shadow-inner">
                            @foreach($allDomains as $domain)
                                <label class="flex items-center gap-2 p-2 hover:bg-white rounded transition-colors cursor-pointer group">
                                    <input type="checkbox" name="selected_domains[]" value="{{ $domain->id }}" x-model="selectedDomains" class="rounded text-[#8704BF] border-gray-300">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#8704BF]">{{ $domain->domain_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="show = false" class="px-6 py-3 bg-gray-200 rounded-xl font-semibold">Cancelar</button>
                        <button type="submit" class="px-6 py-3 bg-[#5F1BF2] text-white rounded-xl font-semibold shadow-lg">Enviar Correos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- WhatsApp Modal -->
    @php
        $wa_templates = [
            'activation' => $settings['wa_activation'] ?? "Hola [Nombre],\n\nSu servicio asociado al dominio *[Dominio]* ha sido activado exitosamente.\n\n¡Gracias por confiar en nosotros!",
            'expiry' => $settings['wa_expiry'] ?? "Hola [Nombre],\n\nLe recordamos que su dominio *[Dominio]* está próximo a vencer.\n\nPor favor, confirme si desea renovarlo para evitar interrupciones.\n\nQuedamos atentos.",
            'promo' => $settings['wa_promo'] ?? "Estimado/a [Nombre],\n\nLe informamos que tiene un saldo pendiente para la renovación de *[Dominio]*.\n\nPor favor, envíenos su comprobante una vez realizado el pago.\n\n¡Gracias!",
        ];
        foreach($customMessages as $msg) {
            $wa_templates['custom_'.$msg->id] = $msg->content;
        }
    @endphp

    <div x-data="{ 
            show: false,
            domainId: null,
            domainName: '',
            phone: '',
            customerName: '',
            message: '',
            currentTemplate: '',
            templates: {{ json_encode($wa_templates) }},
            init() {
                this.$watch('currentTemplate', value => {
                    if (value && this.templates[value]) {
                        let text = this.templates[value];
                        text = text.replace(/\[nombre\]/gi, this.customerName || 'Cliente');
                        text = text.replace(/\[dominio\]/gi, this.domainName || '');
                        text = text.replace(/\{cliente\}/gi, this.customerName || 'Cliente');
                        text = text.replace(/\{dominio\}/gi, this.domainName || '');
                        this.message = text;
                    }
                });
            },
            openModal(event) {
                this.domainId = event.detail.domainId || null;
                this.domainName = event.detail.domainName || '';
                this.phone = event.detail.phone || '';
                this.customerName = event.detail.clientName || '';
                this.message = '';
                this.currentTemplate = '';
                this.show = true;
                if (event.detail.initialTemplate) {
                    setTimeout(() => this.currentTemplate = event.detail.initialTemplate, 10);
                }
            },
            send() {
                if (!this.phone) { alert('Este dominio no tiene un número de teléfono registrado.'); return; }
                const cleanPhone = this.phone.replace(/\D/g, '');
                window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(this.message)}`, '_blank');
                this.show = false;
            }
         }"
         x-show="show"
         @open-whatsapp-modal.window="openModal($event)"
         @keydown.escape.window="show = false"
         class="fixed inset-0 z-[100] overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="show = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 transform transition-all" @click.stop>
                <button type="button" @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                <div class="mb-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#25D366]/10 p-3 rounded-xl"><svg class="w-8 h-8 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 2.17.7 4.19 1.94 5.84L2.5 21.5l3.77-1.35A9.954 9.954 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.5 14c-.6.8-1.5 1-2 1h-.5c-.34 0-1.78-.65-3.5-2.22-1.7-1.55-2.65-3.08-2.65-3.41v-.47c0-.5.2-.85.7-1.35.45-.45.65-.65.8-.95.2-.25.15-.55 0-.85-.15-.3-.7-1.7-.95-2.35-.3-.75-.6-.6-.8-.6h-.65c-.3 0-.8.1-1.2.6-.4.5-1.55 1.5-1.55 3.65 0 2.15 1.6 4.25 1.8 4.5 1.15 1.55 3.32 3.82 5.6 4.49 2.27.67 3.35.54 4.15.44.8-.1 2-1.02 2.25-2.02.25-1 .25-1.85.15-2.02-.1-.15-.35-.25-.75-.45z" clip-rule="evenodd" /></svg></div>
                        <div><h3 class="text-xl font-bold text-gray-900">Enviar WhatsApp</h3><p class="text-sm text-gray-500" x-text="domainName"></p></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-sm" x-show="phone">Enviando a: <span class="font-bold" x-text="phone"></span></div>
                    <select x-model="currentTemplate" class="block w-full border-2 border-gray-100 rounded-xl focus:border-[#25D366] text-gray-900 font-medium">
                        <option value="">Seleccionar plantilla...</option>
                        <option value="activation">✅ Activación</option>
                        <option value="expiry">📅 Vencimiento</option>
                        <option value="promo">💰 Pago</option>
                        @foreach($customMessages as $msg) <option value="custom_{{ $msg->id }}">✨ {{ $msg->title }}</option> @endforeach
                    </select>
                    <textarea x-model="message" rows="5" class="block w-full border-2 border-gray-100 rounded-xl focus:border-[#25D366] p-4 text-gray-900 font-medium"></textarea>
                    <div class="flex gap-3 justify-end pt-4 border-t"><button type="button" @click="show = false" class="px-6 py-3 bg-gray-100 rounded-xl font-semibold">Cancelar</button><button type="button" @click="send()" class="px-6 py-3 bg-[#25D366] text-white rounded-xl font-semibold shadow-lg">Abrir WhatsApp</button></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3"><span class="text-sm font-medium">{{ session('success') }}</span></div>
    @endif

    <!-- AJAX Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const domainsGrid = document.getElementById('domains-grid');
            const loadingIndicator = document.getElementById('loading-indicator');
            let timeout = null;
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(timeout);
                    loadingIndicator.classList.remove('hidden');
                    timeout = setTimeout(() => {
                        const params = new URLSearchParams({ search: e.target.value, status: '{{ request("status") }}' });
                        fetch(`{{ route('domains.index') }}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.text()).then(html => { domainsGrid.innerHTML = html; loadingIndicator.classList.add('hidden'); });
                    }, 400);
                });
            }
        });
    </script>
</x-app-layout>
