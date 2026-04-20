<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Soporte WhatsApp') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="soporteApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ========== CONNECTION PANEL (Browser Mode) ========== --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-white/50">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center shadow-lg"
                                :class="status === 'browser_ready' ? 'bg-gradient-to-br from-green-400 to-green-600' : 'bg-gradient-to-br from-gray-300 to-gray-500'">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    WhatsApp Soporte
                                </h3>
                                <p class="text-sm"
                                    :class="status === 'browser_ready' ? 'text-green-600 font-semibold' : 'text-gray-500'">
                                    <template x-if="status === 'browser_ready'">
                                        <span>WhatsApp se abrira directamente en tu navegador con el mensaje listo para enviar.</span>
                                    </template>
                                    <template x-if="status === 'disconnected'">
                                        <span class="text-gray-500">Preparando el modo navegador...</span>
                                    </template>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full animate-pulse"
                                :class="status === 'browser_ready' ? 'bg-green-500' : 'bg-gray-400'">
                            </div>
                        </div>
                    </div>

                    <div class="text-center py-8">
                        <div class="max-w-2xl mx-auto rounded-2xl border border-green-100 bg-green-50 px-6 py-8">
                            <p class="text-base font-semibold text-green-800">Modo directo por navegador</p>
                            <p class="mt-2 text-sm text-green-700">
                                Este modulo ya no depende de Evolution API ni de una sesion de WhatsApp en el VPS.
                            </p>
                            <p class="mt-2 text-sm text-green-700">
                                Al hacer clic en enviar, se abrira WhatsApp Web o la app instalada con el numero y el mensaje listos.
                            </p>
                            <p x-show="connectionError" x-text="connectionError"
                                class="mt-3 text-xs text-red-500 font-mono bg-white p-2 rounded"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== MESSAGING PANEL ========== --}}
            <div x-show="status === 'browser_ready'" x-transition>
                {{-- Quick Send --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-white/50 mb-6">
                    <div class="bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z" />
                            </svg>
                            <span>Enviar Mensaje por WhatsApp</span>
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Phone + Client Search --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Phone Input --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Número de WhatsApp</label>
                                <div class="flex items-center">
                                    <span
                                        class="inline-flex items-center px-3 py-2.5 rounded-l-lg bg-gray-100 text-gray-600 text-sm font-medium border border-r-0 border-gray-300">
                                        🇵🇪 +51
                                    </span>
                                    <input type="text" x-model="sendPhone"
                                        @input="sendPhone = sendPhone.replace(/\D/g, '')"
                                        class="flex-1 rounded-r-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm text-lg font-medium text-gray-900 bg-white placeholder-gray-400"
                                        placeholder="987654321" maxlength="9">
                                </div>
                            </div>

                            {{-- Client/Domain Search Dropdown --}}
                            <div x-data="{ 
                                searchDomain: '', 
                                showDropdown: false,
                                clients: @js($domains->map(fn($d) => [
                                    'domain' => $d->domain_name,
                                    'phone' => $d->phone,
                                    'emails' => $d->corporate_emails ?? $d->emails,
                                    'user' => $d->client_name ?? $d->user?->name ?? '',
                                    'status' => $d->status,
                                ])->toArray()),
                                get filteredClients() {
                                    if (!this.searchDomain || this.searchDomain.length < 1) return this.clients;
                                    const s = this.searchDomain.toLowerCase();
                                    return this.clients.filter(c => 
                                        c.domain.toLowerCase().includes(s) || 
                                        (c.user && c.user.toLowerCase().includes(s)) ||
                                        (c.phone && c.phone.includes(s))
                                    );
                                }
                            }" class="relative">
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    🔍 Buscar por Dominio / Cliente
                                </label>
                                <input type="text" x-model="searchDomain" @focus="showDropdown = true"
                                    @click.away="setTimeout(() => showDropdown = false, 200)"
                                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm text-sm font-medium text-gray-900 bg-white placeholder-gray-400"
                                    placeholder="Buscar dominio o nombre...">

                                {{-- Dropdown Results --}}
                                <div x-show="showDropdown && filteredClients.length > 0" x-transition
                                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-2xl max-h-64 overflow-y-auto">
                                    <template x-for="(client, idx) in filteredClients" :key="idx">
                                        <button type="button" @click="
                                                if (client.phone) {
                                                    let phone = client.phone.replace(/\D/g, '');
                                                    if (phone.startsWith('51')) phone = phone.substring(2);
                                                    if (phone.length > 9) phone = phone.slice(-9);
                                                    // Set the phone input value and trigger Alpine update
                                                    const phoneInput = document.querySelector('[x-model=\'sendPhone\']');
                                                    if (phoneInput) {
                                                        phoneInput.value = phone;
                                                        phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
                                                    }
                                                }
                                                searchDomain = client.domain;
                                                showDropdown = false;
                                            "
                                            class="w-full text-left px-4 py-3 hover:bg-purple-50 transition-colors border-b border-gray-50 last:border-0 flex items-center space-x-3">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white text-xs font-bold">
                                                <span x-text="client.domain[0]?.toUpperCase() || '?'"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 truncate"
                                                    x-text="client.domain"></p>
                                                <div class="flex items-center space-x-2">
                                                    <span x-show="client.user" class="text-xs text-gray-500 truncate"
                                                        x-text="client.user"></span>
                                                    <span x-show="client.phone"
                                                        class="text-xs text-green-600 font-medium"
                                                        x-text="'📱 ' + client.phone"></span>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                    :class="client.status === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                                    x-text="client.status || 'N/A'"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                {{-- No results --}}
                                <div x-show="showDropdown && searchDomain.length > 0 && filteredClients.length === 0"
                                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-center">
                                    <p class="text-sm text-gray-400">No se encontraron dominios</p>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Numbers --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Números
                                recientes</label>
                            <div id="soporte_recent_numbers" class="flex flex-wrap gap-2">
                                <p class="text-sm text-gray-400">No hay números recientes</p>
                            </div>
                        </div>

                        {{-- Message Content --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-700">Mensaje</label>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-lg bg-yellow-50 border border-yellow-200 text-[11px] text-yellow-700 font-medium">
                                    💡 Las plantillas incluyen saludo automático según la hora → <span
                                        x-text="getSaludo()" class="font-bold text-yellow-900"></span>
                                </span>
                            </div>
                            <textarea x-model="sendMessage" rows="5"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm resize-none text-gray-900 bg-white placeholder-gray-400"
                                placeholder="Escribe tu mensaje aquí o selecciona una plantilla..."></textarea>
                        </div>

                        {{-- Mensajes Guardados --}}
                        <div x-data="{
                            savedMessages: @js($soporteMessages->map(fn($m) => [
                                'id' => $m->id,
                                'name' => $m->title,
                                'body' => $m->content,
                                'createdAt' => $m->created_at->format('d/m/Y'),
                            ])->toArray()),
                            showSaved: false,
                            saveName: '',
                            showSaveForm: false,
                            saving: false,
                            async saveMessage() {
                                if (!this.saveName.trim() || !sendMessage.trim()) return;
                                this.saving = true;
                                try {
                                    const res = await fetch('/soporte/messages', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        },
                                        body: JSON.stringify({
                                            title: this.saveName.trim(),
                                            content: sendMessage
                                        })
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.savedMessages.push({
                                            id: data.message.id,
                                            name: data.message.title,
                                            body: data.message.content,
                                            createdAt: new Date().toLocaleDateString('es-PE')
                                        });
                                        this.saveName = '';
                                        this.showSaveForm = false;
                                    }
                                } catch (e) { console.error(e); }
                                this.saving = false;
                            },
                            loadMessage(msg) {
                                const saludo = getSaludo();
                                let text = msg.body;
                                text = text.replace(/\{\{saludo\}\}/gi, saludo);
                                text = text.replace(/\[saludo\]/gi, saludo);
                                text = text.replace(/\(saludo\)/gi, saludo);
                                sendMessage = text;
                                this.showSaved = false;
                            },
                            async deleteMessage(id) {
                                if (!confirm('¿Eliminar este mensaje?')) return;
                                try {
                                    await fetch('/soporte/messages/' + id, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        }
                                    });
                                    this.savedMessages = this.savedMessages.filter(m => m.id !== id);
                                } catch (e) { console.error(e); }
                            }
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-gray-500 uppercase">💬 Mensajes
                                    Guardados</label>
                                <div class="flex items-center space-x-2">
                                    {{-- Botón Guardar Mensaje --}}
                                    <button @click="showSaveForm = !showSaveForm"
                                        :disabled="!sendMessage || sendMessage.trim() === ''"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
                                        :class="showSaveForm ? 'bg-gray-200 text-gray-600' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 border border-purple-300'">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        <span x-text="showSaveForm ? 'Cancelar' : 'Guardar Mensaje'"></span>
                                    </button>
                                    {{-- Botón Ver Guardados --}}
                                    <button @click="showSaved = !showSaved"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200"
                                        :class="showSaved ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-700 hover:bg-blue-200 border border-blue-300'">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <span x-text="'Cargar (' + savedMessages.length + ')'"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- Save Form --}}
                            <div x-show="showSaveForm" x-transition
                                class="mb-3 p-3 bg-purple-50 rounded-xl border border-purple-200">
                                <div class="flex items-center space-x-2">
                                    <input type="text" x-model="saveName" @keydown.enter="saveMessage()"
                                        class="flex-1 rounded-lg border-purple-300 focus:border-purple-500 focus:ring-purple-500 text-sm text-gray-900 bg-white placeholder-gray-400"
                                        placeholder="Nombre del mensaje (ej: Aviso hosting)">
                                    <button @click="saveMessage()" :disabled="!saveName.trim() || saving"
                                        class="px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-40">
                                        <span x-show="!saving">✓ Guardar</span>
                                        <span x-show="saving">Guardando...</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-purple-500 mt-2">
                                    💡 Tip: Usa <code
                                        class="bg-purple-100 px-1 rounded font-mono font-bold">@{{saludo}}</code> en tu
                                    mensaje → se reemplaza por
                                    <span x-text="getSaludo()" class="font-bold"></span> al cargarlo.
                                </p>
                            </div>

                            {{-- Saved Messages List --}}
                            <div x-show="showSaved" x-transition class="space-y-2 max-h-52 overflow-y-auto">
                                <template x-if="savedMessages.length === 0">
                                    <div
                                        class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <p class="text-sm text-gray-400">No hay mensajes guardados aún</p>
                                        <p class="text-xs text-gray-300 mt-1">Escribe un mensaje y dale "Guardar"</p>
                                    </div>
                                </template>
                                <template x-for="msg in savedMessages" :key="msg.id">
                                    <div class="flex items-center space-x-2 p-3 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 group cursor-pointer"
                                        @click="loadMessage(msg)">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            <span x-text="msg.name[0]?.toUpperCase() || '?'"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate" x-text="msg.name"></p>
                                            <p class="text-[11px] text-gray-400 truncate"
                                                x-text="msg.body.substring(0, 80) + '...'"></p>
                                        </div>
                                        <div class="flex items-center space-x-1 flex-shrink-0">
                                            <span class="text-[10px] text-gray-300" x-text="msg.createdAt"></span>
                                            <button @click.stop="deleteMessage(msg.id)"
                                                class="opacity-0 group-hover:opacity-100 p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Send Button --}}
                        <div class="flex items-center justify-between pt-2">
                            <p class="text-xs text-gray-400" x-show="lastSentAt">
                                Último envío: <span x-text="lastSentAt" class="font-medium"></span>
                            </p>
                            <button @click="sendWhatsApp()" :disabled="sending || !sendPhone || !sendMessage"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] text-white font-bold rounded-xl shadow-lg shadow-[#8704BF]/30 hover:shadow-xl hover:shadow-[#8704BF]/40 hover:opacity-90 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 disabled:hover:scale-100">
                                <template x-if="!sending">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                                        </svg>
                                        Enviar por WhatsApp
                                    </span>
                                </template>
                                <template x-if="sending">
                                    <span class="flex items-center">
                                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Enviando...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ========== DIRECT MODE INFO ========== --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-white/50">
                    <div
                        class="bg-gradient-to-r from-[#5F1BF2] to-[#8704BF] px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span>Modo directo</span>
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Que cambia en este modo:</p>
                            <ul class="mt-3 space-y-2 list-disc list-inside">
                                <li>El VPS no envia mensajes por su cuenta.</li>
                                <li>El navegador abre WhatsApp con el texto precargado.</li>
                                <li>No se listan chats ni se requiere QR.</li>
                                <li>Los numeros recientes siguen guardandose en tu navegador.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div id="soporte-toast" class="fixed top-4 right-4 z-50 hidden">
        <div id="soporte-toast-content"
            class="p-4 rounded-xl shadow-2xl text-white font-medium text-sm flex items-center space-x-2">
        </div>
    </div>

    <script>
        function soporteApp() {
            return {
                // Connection
                status: 'browser_ready',
                qrCode: null,
                clientName: '',
                clientPhone: '',
                pollInterval: null,

                // Messaging
                sendPhone: '',
                sendMessage: '',
                sending: false,
                lastSentAt: '',

                // Chats
                connectionError: null,
                csrfToken: document.querySelector('meta[name=csrf-token]')?.content || '',

                init() {
                    this.loadRecentNumbers();
                },

                async sendWhatsApp() {
                    if (!this.sendPhone || !this.sendMessage) return;

                    this.sending = true;

                    // Apply greeting variable - replace all variations
                    let finalMessage = this.sendMessage
                        .replace(/\{\{saludo\}\}/gi, this.getSaludo())
                        .replace(/@\{\{saludo\}\}/gi, this.getSaludo())
                        .replace(/\(saludo\)/gi, this.getSaludo())
                        .replace(/\[saludo\]/gi, this.getSaludo());

                    try {
                        const cleanPhone = this.sendPhone.replace(/\D/g, '');
                        const whatsappNumber = cleanPhone.length === 9 ? `51${cleanPhone}` : cleanPhone;
                        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(finalMessage)}`;

                        window.open(whatsappUrl, '_blank', 'noopener');

                        this.showToast('✅ WhatsApp abierto correctamente', 'success');
                        this.lastSentAt = new Date().toLocaleTimeString('es-PE');
                        this.saveRecentNumber(cleanPhone);
                        this.sendMessage = '';

                        await fetch('{{ route("messages.whatsapp.log") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({
                                number: whatsappNumber,
                                content: finalMessage
                            })
                        });
                    } catch (e) {
                        console.error('WhatsApp open error:', e);
                        this.showToast('❌ No se pudo abrir WhatsApp', 'error');
                    }

                    this.sending = false;
                },

                quickSend(phone, name) {
                    this.sendPhone = phone;
                    this.sendMessage = this.getSaludo() + ` ${name}, `;
                    document.querySelector('[x-model="sendMessage"]')?.focus();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                getSaludo() {
                    const hour = new Date().getHours();
                    if (hour >= 5 && hour < 12) return 'Buenos días';
                    if (hour >= 12 && hour < 19) return 'Buenas tardes';
                    return 'Buenas noches';
                },

                // Recent Numbers (localStorage)
                saveRecentNumber(number) {
                    let recent = JSON.parse(localStorage.getItem('soporte_recent_numbers') || '[]');
                    recent = recent.filter(n => n !== number);
                    recent.unshift(number);
                    recent = recent.slice(0, 8);
                    localStorage.setItem('soporte_recent_numbers', JSON.stringify(recent));
                    this.loadRecentNumbers();
                },

                loadRecentNumbers() {
                    const recent = JSON.parse(localStorage.getItem('soporte_recent_numbers') || '[]');
                    const container = document.getElementById('soporte_recent_numbers');
                    if (!container) return;

                    if (recent.length === 0) {
                        container.innerHTML = '<p class="text-sm text-gray-400">No hay números recientes</p>';
                        return;
                    }

                    container.innerHTML = recent.map(n => `
                        <button onclick="document.querySelector('[x-model=\\'sendPhone\\']').value = '${n}'; document.querySelector('[x-model=\\'sendPhone\\']').dispatchEvent(new Event('input'))"
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-green-50 to-green-100 text-green-700 hover:from-green-100 hover:to-green-200 transition-all duration-200 border border-green-200 shadow-sm hover:shadow transform hover:scale-105">
                            📱 +51${n}
                        </button>
                    `).join('');
                },

                showToast(message, type) {
                    const toast = document.getElementById('soporte-toast');
                    const content = document.getElementById('soporte-toast-content'); const colors = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        info: 'bg-blue-500'
                    };

                    content.className = `p-4 rounded-xl shadow-2xl text-white font-medium text-sm flex items-center space-x-2 ${colors[type] || colors.info}`;
                    content.innerHTML = `<span>${message}</span>`;
                    toast.classList.remove('hidden');

                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 4000);
                }
            };
        }
    </script>

    <style>
        /* Custom scrollbar for chats */
        .max-h-\[400px\]::-webkit-scrollbar {
            width: 6px;
        }

        .max-h-\[400px\]::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .max-h-\[400px\]::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 10px;
        }

        .max-h-\[400px\]::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        /* Force input text visibility */
        .py-8 input[type="text"],
        .py-8 textarea {
            color: #111827 !important;
            background-color: #ffffff !important;
        }

        .py-8 input[type="text"]::placeholder,
        .py-8 textarea::placeholder {
            color: #9ca3af !important;
        }
    </style>
</x-app-layout>
