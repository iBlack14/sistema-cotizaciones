<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Seguimiento de Cotizaciones') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showFilters: false, exportType: 'excel', openExportModal(type) { this.exportType = type; this.showFilters = true; } }">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-white/50">
                <div class="p-8">
                    <!-- Header with Export Button -->
                    <div class="flex justify-between items-center mb-6">
                        <h3
                            class="text-2xl font-bold bg-gradient-to-r from-[#8704BF] to-[#F2059F] bg-clip-text text-transparent">
                            Listado de Cotizaciones
                        </h3>
                        <div class="flex gap-2">
                            <button @click="openExportModal('excel')"
                                class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] border-0 rounded-xl font-semibold text-sm text-white uppercase tracking-widest shadow-lg shadow-[#8704BF]/30 hover:shadow-xl hover:shadow-[#8704BF]/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BF1F6A] transition-all duration-150 hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excel
                            </button>
                            <button @click="openExportModal('word')"
                                class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 border-0 rounded-xl font-semibold text-sm text-white uppercase tracking-widest shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-all duration-150 hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Word
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="relative max-w-xl">
                            <input id="quotation-search-input" type="text" value="{{ request('search') }}"
                                placeholder="Buscar cotización..."
                                class="w-full rounded-2xl border-gray-300 text-gray-900 placeholder-gray-400 focus:border-[#8704BF] focus:ring-[#8704BF] shadow-sm pr-12" />
                            <button id="quotation-search-button" type="button"

                                class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] text-white shadow-md hover:opacity-95 transition-all"
                                aria-label="Buscar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div id="quotations-table-wrapper">                    <div class="flex flex-wrap items-center gap-4 mb-6 text-xs text-gray-500 bg-gray-50/50 p-4 rounded-xl border border-gray-100 w-full">
                        <span class="font-bold text-gray-700 uppercase tracking-wider mr-2">Estados:</span>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 ring-2 ring-red-100"></span> Pendiente
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 ring-2 ring-yellow-100"></span> En Proceso
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 ring-2 ring-green-100"></span> Completado
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></span> Confirmado
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-700 ring-2 ring-gray-200"></span> Denegado
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-2 ring-blue-100"></span> Llamadas
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-2 ring-indigo-100"></span> Múltiples Llamadas
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-gray-100 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 ring-2 ring-teal-100"></span> Com. Pendiente
                        </div>
                    </div>
                        </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <thead>
                                <tr style="background: linear-gradient(to right, #7e22ce, #c026d3); color: white;">
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider rounded-tl-lg w-[12%]">
                                        Estado
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[26%]">
                                        Empresa</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[18%]">
                                        Servicio Principal</th>
                                    <th scope="col"
                                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[8%]">
                                        Fecha Envío</th>
                                    <th scope="col"
                                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[10%]">
                                        Fecha Respuesta</th>
                                    <th scope="col"
                                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[10%]">
                                        Mensaje</th>
                                    <th scope="col"
                                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-[8%]">
                                        Reenvío</th>
                                    <th scope="col"
                                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider rounded-tr-lg w-[8%]">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100" x-data="{ activeId: null }">
                                @forelse($quotations as $quotation)
                                    @php
                                        $rawStatus = strtolower(trim((string) ($quotation->status ?? '')));
                                        $statusMap = [
                                            '' => 'pending',
                                            'pendiente' => 'pending',
                                            'pending' => 'pending',
                                            'en proceso' => 'in_process',
                                            'en_proceso' => 'in_process',
                                            'in process' => 'in_process',
                                            'in_process' => 'in_process',
                                            'completado' => 'completed',
                                            'completada' => 'completed',
                                            'completed' => 'completed',
                                            'confirmado' => 'confirmed',
                                            'confirmada' => 'confirmed',
                                            'confirmed' => 'confirmed',
                                            'denegado' => 'denied',
                                            'denied' => 'denied',
                                            'llamadas' => 'calls',
                                            'calls' => 'calls',
                                            'multiples llamadas' => 'multiple_calls',
                                            'multiple_calls' => 'multiple_calls',
                                            'comunicacion pendiente' => 'pending_communication',
                                            'pending_communication' => 'pending_communication',
                                        ];
                                        $normalizedStatus = $statusMap[$rawStatus] ?? 'pending';
                                    @endphp
                                    <tr class="transition-all duration-200 border-b border-gray-200 hover:shadow-md"
                                        :style="rowStyle()"
                                        x-data="{ 
                                        responseDate: @js($quotation->response_date),
                                        message: @js($quotation->follow_up_message),
                                        note: @js($quotation->follow_up_note),
                                        status: @js($normalizedStatus),
                                        loading: false,
                                        init() {
                                            if (typeof this.status === 'string') {
                                                this.status = this.status.trim().toLowerCase();
                                            }
                                            this.ensureValidStatus();
                                        },
                                        ensureValidStatus() {
                                            if (!['pending', 'in_process', 'completed', 'confirmed', 'denied', 'calls', 'multiple_calls', 'pending_communication'].includes(this.status)) {
                                                this.status = 'pending';
                                            }
                                        },
                                        rowStyle() {
                                            if (this.status === 'completed') return 'background-color:#dcfce7;border-left:4px solid #16a34a;';
                                            if (this.status === 'confirmed') return 'background-color:#ecfdf5;border-left:4px solid #059669;';
                                            if (this.status === 'in_process') return 'background-color:#fef9c3;border-left:4px solid #ca8a04;';
                                            if (this.status === 'denied') return 'background-color:#d1d5db;border-left:4px solid #374151;';
                                            if (this.status === 'calls') return 'background-color:#dbeafe;border-left:4px solid #2563eb;';
                                            if (this.status === 'multiple_calls') return 'background-color:#e0e7ff;border-left:4px solid #4f46e5;';
                                            if (this.status === 'pending_communication') return 'background-color:#ccfbf1;border-left:4px solid #0d9488;';
                                            return 'background-color:#fee2e2;border-left:4px solid #dc2626;';
                                        },
                                        updateStatus() {
                                            fetch('{{ route('quotations.update', $quotation) }}', {
                                                method: 'PUT',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ status: this.status })
                                            })
                                            .then(response => response.json())
                                            .catch(error => console.error('Error updating status:', error));
                                        },
                                        loading: false,
                                        update() {
                                            this.loading = true;
                                            fetch('{{ route('quotations.update', $quotation) }}', {
                                                method: 'PUT',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    response_date: this.responseDate,
                                                    follow_up_message: this.message,
                                                    follow_up_note: this.note
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                this.loading = false;
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                this.loading = false;
                                            });
                                        },
                                        sendEmail(mode) {
                                            if (!confirm('¿Estás seguro de enviar el correo?')) return;

                                            let type = this.message || 'initial';
                                            if (mode === 'resend') type = 'resend';
                                            const shouldOpenWhatsapp = mode === 'combined' || mode === 'resend';
                                            const whatsappWindow = shouldOpenWhatsapp ? window.open('about:blank', '_blank') : null;

                                            this.loading = true;
                                            fetch('{{ route('quotations.send-email', $quotation) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    email: @js($quotation->client_email),
                                                    type: type
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                this.loading = false;
                                                if (!data || data.success === false) {
                                                    if (whatsappWindow && !whatsappWindow.closed) whatsappWindow.close();
                                                    alert(data?.message || 'No se pudo enviar el correo.');
                                                    return;
                                                }
                                                alert(data.message || 'Correo enviado correctamente');
                                                if (mode === 'combined' || mode === 'resend') {
                                                    this.openWhatsApp(mode, whatsappWindow);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                this.loading = false;
                                                if (whatsappWindow && !whatsappWindow.closed) whatsappWindow.close();
                                                alert('Error al enviar el correo. Verifica que el cliente tenga un email válido.');
                                            });
                                        },
                                        openWhatsApp(mode = null, targetWindow = null) {
                                            let phone = (@js($quotation->client_phone ?? '') || '').replace(/\D/g, '');
                                            if (!phone.startsWith('51') && phone.length === 9) {
                                                phone = '51' + phone;
                                            }
                                            if (!phone || phone.length < 9) {
                                                if (targetWindow && !targetWindow.closed) targetWindow.close();
                                                alert('El cliente no tiene un número de WhatsApp válido.');
                                                return;
                                            }

                                            let template = '';
                                            const customMsgObj = @js(isset($customMessages) ? $customMessages->pluck('content', 'title') : collect());
                                            const templates = {
                                                resend: @js($settings['resend_whatsapp_message'] ?? ''),
                                                confirmation: @js($settings['confirmation_whatsapp_message'] ?? ''),
                                                service: @js($settings['service_whatsapp_message'] ?? ''),
                                                access: @js($settings['access_whatsapp_message'] ?? ''),
                                                quotation: @js($settings['quotation_whatsapp_message'] ?? ''),
                                            };
                                            
                                            if (mode === 'resend') {
                                                template = templates.resend;
                                            } else if (this.message === 'Confirmación') {
                                                template = templates.confirmation;
                                            } else if (this.message === 'Servicio') {
                                                template = templates.service;
                                            } else if (this.message === 'Acceso de su servicio') {
                                                template = templates.access;
                                            } else if (customMsgObj[this.message]) {
                                                template = customMsgObj[this.message] || '';
                                            } else {
                                                template = templates.quotation;
                                            }

                                            // Fallbacks if settings are empty
                                            if (!template) {
                                                if (mode === 'resend') template = 'Hola [Nombre], te reenvío la cotización. Saludos.';
                                                else if (this.message === 'Confirmación') template = 'Hola [Nombre], ¿pudiste revisar la cotización?';
                                                else if (this.message === 'Servicio') template = 'Hola [Nombre], coordinemos el servicio.';
                                                else if (this.message === 'Acceso de su servicio') template = 'Hola [Nombre], necesitamos accesos.';
                                                else template = 'Hola [Nombre], adjunto cotización.';
                                            }

                                            // Replacements
                                            const replacementValues = {
                                                nombre: @js($quotation->client_name ?? $quotation->client_company ?? ''),
                                                empresa: @js($quotation->client_company ?? ''),
                                                ruc: @js($quotation->client_ruc ?? ''),
                                                fecha: @js(\Carbon\Carbon::parse($quotation->date)->format('d/m/Y')),
                                                servicio: @js($quotation->items->first()->service_name ?? ''),
                                                total: @js(number_format($quotation->total, 2)),
                                                link: @js($quotation->slug ? route('quotations.public', $quotation->slug) : \Illuminate\Support\Facades\URL::signedRoute('quotations.download', $quotation)),
                                            };
                                            let messageText = template
                                                .replace(/\[Nombre\]/g, replacementValues.nombre)
                                                .replace(/\[Empresa\]/g, replacementValues.empresa)
                                                .replace(/\[RUC\]/g, replacementValues.ruc)
                                                .replace(/\[Fecha\]/g, replacementValues.fecha)
                                                .replace(/\[Servicio\]/g, replacementValues.servicio)
                                                .replace(/\[Total\]/g, replacementValues.total)
                                                .replace(/\[Link\]/g, replacementValues.link);

                                            let url = `https://wa.me/${phone}?text=${encodeURIComponent(messageText)}`;
                                            if (targetWindow && !targetWindow.closed) {
                                                targetWindow.location.href = url;
                                                targetWindow.focus();
                                            } else {
                                                window.open(url, '_blank');
                                            }
                                        }
                                    }">
                                        <td class="px-3 py-4 text-sm font-bold text-center">
                                            <select :value="status"
                                                @change="status = ($event.target.value || 'pending'); ensureValidStatus(); updateStatus()"
                                                :class="{
                                                    'text-red-800 border-red-200 bg-red-50': status === 'pending',
                                                    'text-yellow-800 border-yellow-200 bg-yellow-50': status === 'in_process',
                                                    'text-green-800 border-green-200 bg-green-50': status === 'completed',
                                                    'text-emerald-800 border-emerald-200 bg-emerald-50': status === 'confirmed',
                                                    'text-gray-900 border-gray-400 bg-gray-200': status === 'denied',
                                                    'text-blue-800 border-blue-200 bg-blue-50': status === 'calls',
                                                    'text-indigo-800 border-indigo-200 bg-indigo-50': status === 'multiple_calls',
                                                    'text-teal-800 border-teal-200 bg-teal-50': status === 'pending_communication',
                                                }"
                                                class="w-full min-w-[150px] py-2 px-3 text-sm font-bold uppercase tracking-wide border rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-fuchsia-400 focus:border-fuchsia-400"
                                                style="color:#111827 !important; -webkit-text-fill-color:#111827 !important;">
                                                <option value="pending">Pendiente</option>
                                                <option value="in_process">En Proceso</option>
                                                <option value="completed">Completado</option>
                                                <option value="confirmed">Confirmado</option>
                                                <option value="denied">Denegado</option>
                                                <option value="calls">Llamadas</option>
                                                <option value="multiple_calls">Múltiples Llamadas</option>
                                                <option value="pending_communication">Com. Pendiente</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-4 text-sm font-medium text-gray-900 relative">
                                            <div class="flex items-center space-x-2">
                                                <span>{{ $quotation->client_company ?? $quotation->client_name }}</span>
                                                <button
                                                    @click="activeId = activeId === {{ $quotation->id }} ? null : {{ $quotation->id }}"
                                                    class="text-gray-400 hover:text-fuchsia-600 focus:outline-none transition-colors"
                                                    :class="{ 'text-fuchsia-600': activeId === {{ $quotation->id }} }">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 transform transition-transform duration-200"
                                                        :class="{ 'rotate-180': activeId === {{ $quotation->id }} }"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div x-show="activeId === {{ $quotation->id }}"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 -translate-y-2"
                                                class="mt-3 w-full bg-gray-50 rounded-r-md border-l-4 border-fuchsia-600 shadow-sm p-4"
                                                style="display: none;">
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-xs text-gray-600">
                                                    <div
                                                        class="col-span-1 sm:col-span-2 pb-1 border-b border-gray-200 mb-1 font-semibold text-fuchsia-700 uppercase tracking-wider">
                                                        Detalles del Cliente
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-bold text-gray-500 uppercase text-[10px]">Contacto</span>
                                                        <span
                                                            class="text-gray-800 font-medium">{{ $quotation->client_name }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-bold text-gray-500 uppercase text-[10px]">Email</span>
                                                        <span class="text-gray-800 truncate"
                                                            title="{{ $quotation->client_email }}">{{ $quotation->client_email }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-bold text-gray-500 uppercase text-[10px]">Teléfono</span>
                                                        <span class="text-gray-800">{{ $quotation->client_phone }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-bold text-gray-500 uppercase text-[10px]">RUC</span>
                                                        <span class="text-gray-800">{{ $quotation->client_ruc }}</span>
                                                    </div>
                                                    <div class="col-span-1 sm:col-span-2 flex flex-col mt-1">
                                                        <span
                                                            class="font-bold text-gray-500 uppercase text-[10px]">Dirección</span>
                                                        <span class="text-gray-800 truncate"
                                                            title="{{ $quotation->client_address }}">{{ $quotation->client_address }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                            {{ $quotation->items->first()->service_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                {{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $quotation->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-4 text-sm">
                                            <div class="flex flex-col space-y-2 max-w-[150px]">
                                                <div class="relative group">
                                                    <input type="date" x-model="responseDate" @change="update()"
                                                        class="block w-full text-xs text-gray-700 bg-gray-50/50 border border-gray-200 rounded-lg focus:ring-fuchsia-500 focus:border-fuchsia-500 transition-colors duration-200 px-2 py-1.5"
                                                        placeholder="dd/mm/aaaa">
                                                </div>
                                                <textarea x-model="note" @change="update()" rows="2"
                                                    class="w-full text-xs text-gray-700 bg-gray-50/50 border border-gray-200 rounded-lg focus:ring-fuchsia-500 focus:border-fuchsia-500 transition-colors duration-200 resize-none shadow-sm placeholder-gray-400 p-2"
                                                    placeholder="Nota recordatorio..."></textarea>
                                            </div>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">
                                            <select x-model="message" @change="update()"
                                                class="w-full text-xs text-gray-700 bg-gray-50/50 border border-gray-200 rounded-lg focus:ring-fuchsia-500 focus:border-fuchsia-500 transition-colors duration-200 shadow-sm py-2">
                                                <option value="">Seleccionar mensaje...</option>
                                                <optgroup label="Sistemas Predeterminados">
                                                    <option value="Confirmación">Confirmación</option>
                                                    <option value="Servicio">Servicio</option>
                                                    <option value="Acceso de su servicio">Acceso de su servicio</option>
                                                </optgroup>
                                                @if(isset($customMessages) && $customMessages->count() > 0)
                                                    <optgroup label="Mensajes Guardados">
                                                        @foreach($customMessages as $customMsg)
                                                            <option value="{{ $customMsg->title }}">{{ $customMsg->title }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            </select>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex flex-row space-x-2">
                                                <a href="{{ route('quotations.show', $quotation) }}" target="_blank"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-white rounded-lg transition-all duration-200 text-xs font-bold shadow-sm bg-fuchsia-600 hover:bg-fuchsia-700 hover:shadow-fuchsia-200 hover:shadow-md">
                                                    Ver Cotización
                                                </a>
                                                <button @click="sendEmail('resend')"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-white rounded-lg transition-all duration-200 text-xs font-bold shadow-sm bg-pink-600 hover:bg-pink-700 hover:shadow-pink-200 hover:shadow-md">
                                                    Reenviar
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-3">
                                                <!-- Email Button -->
                                                <button @click="sendEmail('email')"
                                                    class="text-gray-400 hover:text-fuchsia-600 transition-colors p-2 rounded-full hover:bg-fuchsia-50"
                                                    title="Enviar Email">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                    </svg>
                                                </button>

                                                <!-- WhatsApp Button -->
                                                <button @click="openWhatsApp()"
                                                    class="text-gray-400 hover:text-green-600 transition-colors p-2 rounded-full hover:bg-green-50"
                                                    title="Enviar WhatsApp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                                        <path
                                                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                                    </svg>
                                                </button>

                                                <!-- Combined Button -->
                                                <button @click="sendEmail('combined')"
                                                    class="text-gray-400 hover:text-purple-700 transition-colors p-2 rounded-full hover:bg-purple-50"
                                                    title="Email + WhatsApp">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                                                        </svg>
                                                    </div>
                                                </button>

                                                <!-- Download PDF Button -->
                                                <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('quotations.download', $quotation) }}" target="_blank"
                                                    class="text-gray-400 hover:text-blue-600 transition-colors p-2 rounded-full hover:bg-blue-50"
                                                    title="Descargar PDF">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                    </svg>
                                                </a>

                                                <!-- Delete Button -->
                                                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                                                    class="inline-block"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar esta cotización?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-full hover:bg-red-50"
                                                        title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-gray-500 bg-white">
                                            No se encontraron cotizaciones con los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <p class="text-sm text-gray-600">
                            Mostrando {{ $quotations->count() }} de {{ $quotations->total() }} cotizaciones
                        </p>
                        <div>
                            {{ $quotations->links() }}
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Export Modal -->
    <div x-show="showFilters" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showFilters" x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showFilters" x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-fuchsia-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-fuchsia-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-4" id="modal-title">
                                Opciones de Exportación
                            </h3>
                            
                            <form x-bind:action="exportType === 'excel' ? '{{ route('quotations.export') }}' : '{{ route('quotations.export-word') }}'" method="GET">
                                <!-- Date Range -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Inicio</label>
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Fin</label>
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="mb-4">
<label class="block text-sm font-semibold text-gray-700 mb-2">Buscar Cliente (Nombre,
                                    Empresa o ID)</label>
                                <input type="text" name="client" placeholder="Ej: Maria, Acme Corp, 12345..."
                                    class="w-full rounded-lg border-gray-300 text-gray-900 placeholder-gray-400 focus:border-[#8704BF] focus:ring-[#8704BF] shadow-sm">
                            </div>

                            <!-- Columns Selection -->
                                <div class="border-t pt-4">
                                    <div class="flex justify-between items-center mb-3">
                                    <label class="block text-sm font-semibold text-gray-700">Columnas a Exportar</label>
                                    <button type="button" onclick="toggleAllColumns()"
                                        class="text-xs text-[#8704BF] hover:text-[#F2059F] font-medium">
                                        Seleccionar/Deseleccionar Todas
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto overflow-x-hidden p-2 bg-gray-50 rounded-lg">
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="id" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">ID</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="empresa" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Empresa/Cliente</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="contacto" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Contacto</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="email" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Email</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="telefono" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Teléfono</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="ruc" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">RUC</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="direccion" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Dirección</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="servicio" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Servicio Principal</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="descripcion" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Descripción</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="total" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Total</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="fecha_envio" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Fecha Envío</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="fecha_respuesta" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Fecha Respuesta</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="mensaje" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Mensaje</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="nota" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Nota</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="usuario" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Usuario</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="estado" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Estado</span>
                                    </label>
                                    <label
                                        class="flex items-center space-x-2 text-sm hover:bg-white px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="columns[]" value="eliminado" checked
                                            class="column-checkbox rounded text-[#8704BF] focus:ring-[#8704BF]">
                                        <span class="text-gray-700">Eliminado</span>
                                    </label>
                                </div>
                            </div>

                            <script>
                                function toggleAllColumns() {
                                    const checkboxes = document.querySelectorAll('.column-checkbox');
                                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                                    checkboxes.forEach(cb => cb.checked = !allChecked);
                                }
                            </script>
                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] border-0 rounded-xl font-semibold text-sm text-white uppercase tracking-widest shadow-lg shadow-[#8704BF]/30 hover:shadow-xl hover:shadow-[#8704BF]/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BF1F6A] transition-all duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-text="exportType === 'excel' ? 'Exportar Excel' : 'Exportar Word'"></span>
                            </button>
                            <button type="button" @click="showFilters = false"
                                class="w-full sm:w-auto inline-flex justify-center px-6 py-3 bg-white border-2 border-gray-300 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('quotation-search-input');
            const button = document.getElementById('quotation-search-button');
            const wrapperId = 'quotations-table-wrapper';
            let debounceTimer = null;

            if (!input || !button) return;

            const updateTableFromUrl = async (url, pushHistory = false) => {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const nextWrapper = doc.getElementById(wrapperId);
                    const currentWrapper = document.getElementById(wrapperId);

                    if (nextWrapper && currentWrapper) {
                        currentWrapper.innerHTML = nextWrapper.innerHTML;
                        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                            window.Alpine.initTree(currentWrapper);
                        }
                        if (pushHistory) {
                            window.history.replaceState({}, '', url);
                        }
                    }
                } catch (error) {
                    console.error('Error en búsqueda AJAX:', error);
                }
            };

            const buildSearchUrl = (value) => {
                const url = new URL(window.location.href);
                if (value && value.trim() !== '') {
                    url.searchParams.set('search', value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                return url.toString();
            };

            const runSearch = () => {
                const url = buildSearchUrl(input.value);
                updateTableFromUrl(url, true);
            };

            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runSearch, 350);
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    runSearch();
                }
            });

            button.addEventListener('click', runSearch);

            document.addEventListener('click', (event) => {
                const link = event.target.closest('#' + wrapperId + ' .pagination a');
                if (!link) return;

                event.preventDefault();
                const url = new URL(link.href);
                if (input.value.trim() !== '') {
                    url.searchParams.set('search', input.value.trim());
                }
                updateTableFromUrl(url.toString(), true);
            });
        })();
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
    <div x-data="{
            open: false,
            src: '',
            loading: false,
            zoom: 1,
            baseW: 816,
            baseH: 1056,
            fit: true,
            fitToWidth() {
                if (!this.$refs.viewport) return;
                const w = this.$refs.viewport.clientWidth || 0;
                // Add some padding (e.g. 32px or 2rem)
                const target = Math.max(0.5, Math.min(2.5, (w - 32) / this.baseW));
                this.zoom = target;
            },
            adjustScroll(factor) {
                 if (!this.$refs.viewport) return;
                 const el = this.$refs.viewport;
                 
                 // Current center in content coordinates
                 const cx = el.scrollLeft + el.clientWidth / 2;
                 const cy = el.scrollTop + el.clientHeight / 2;
                 
                 // Determine change ratio
                 // We don't have the old zoom easily available unless we pass it or store it.
                 // But we can approximate the scroll shift.
                 // Actually, simpler: Calculate ratio relative to OLD zoom? 
                 // We updated this.zoom already? No, let's update zoom inside.
            },
            zoomIn() {
                const oldZoom = this.zoom;
                this.fit = false;
                this.zoom = Math.min(this.zoom + 0.25, 3.5);
                this.updateScroll(oldZoom, this.zoom);
            },
            zoomOut() {
                const oldZoom = this.zoom;
                this.fit = false;
                this.zoom = Math.max(this.zoom - 0.25, 0.4);
                this.updateScroll(oldZoom, this.zoom);
            },
            updateScroll(oldZ, newZ) {
                this.$nextTick(() => {
                    const el = this.$refs.viewport;
                    if (!el) return;
                    
                    const ratio = newZ / oldZ;
                    
                    // Center point should remain stable
                    const w = el.clientWidth;
                    const h = el.clientHeight;
                    
                    // Scroll Left
                    const centerX = el.scrollLeft + w / 2;
                    const newCenterX = centerX * ratio;
                    el.scrollLeft = newCenterX - w / 2;

                    // Scroll Top
                    const centerY = el.scrollTop + h / 2;
                    const newCenterY = centerY * ratio;
                    el.scrollTop = newCenterY - h / 2;
                });
            },
            toggleZoom() {
                this.fit = !this.fit;
                if (this.fit) {
                    this.fitToWidth();
                } else {
                    this.zoom = 1.0;
                }
            },
            close() {
                this.open = false;
            }
        }" x-init="$watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : '';
            if (!value) {
                loading = false;
                src = '';
                zoom = 1;
                fit = true;
            }
        })" @open-preview.window="
            open = true;
            src = $event.detail.url;
            loading = true;
            fit = true;
            $nextTick(() => fitToWidth());
        " @keydown.escape.window="open ? close() : null" x-show="open" style="display: none;"
        class="fixed inset-0 z-50" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-black/60 backdrop-blur-[2px]" @click="close()"></div>

        <div class="fixed inset-0 p-1 sm:p-2 flex items-center justify-center">
            <div x-show="open" x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
                class="w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-white/20"
                style="height: min(94vh, 1020px); display: flex; flex-direction: column;">

                <div
                    class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-[#5F1BF2]/10 to-[#F2059F]/10 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex items-center gap-3">
                        <button @click="close()" type="button"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white/70 transition-colors">
                            <span class="sr-only">Cerrar</span>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="zoomOut()" type="button"
                            class="hidden sm:inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white/70 transition-colors"
                            title="Reducir">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <button @click="zoomIn()" type="button"
                            class="hidden sm:inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white/70 transition-colors"
                            title="Aumentar">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 min-h-0 relative bg-white">
                    <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100/70">
                        <div
                            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white shadow border border-gray-200">
                            <div
                                class="w-5 h-5 rounded-full border-2 border-gray-300 border-t-fuchsia-600 animate-spin">
                            </div>
                            <div class="text-sm text-gray-700 font-medium">Cargando vista previa...</div>
                        </div>
                    </div>

                    <div class="min-h-0 h-full w-full overflow-auto" x-ref="viewport"
                        @resize.window="fit ? fitToWidth() : null">
                        <div class="min-h-full w-full flex p-0">
                            <div class="bg-white overflow-hidden relative m-auto shadow-lg"
                                :style="'width: ' + (baseW * zoom) + 'px;'">
                                <div class="origin-top-left"
                                    :style="'width: ' + (baseW * zoom) + 'px; height: ' + (baseH * zoom) + 'px; overflow: hidden; position: relative;'">
                                    <iframe x-ref="frame" :src="src" @load="loading = false" class="border-0"
                                        :style="'width: ' + baseW + 'px; height: ' + baseH + 'px; position: absolute; top: 0; left: 0; transform: scale(' + zoom + '); transform-origin: top left; background: white;'">
                                    </iframe>
                                    <!-- Interaction Layer -->
                                    <div @click="zoomIn()" @contextmenu.prevent="zoomOut()"
                                        class="absolute inset-0 z-20 cursor-zoom-in"
                                        title="Click izquierdo: Aumentar | Click derecho: Reducir">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
