@if($domains->count() > 0)
    <div class="grid grid-cols-1 gap-8">
        <style>
            @keyframes soft-pulse-red {
                0%, 100% { transform: scale(1); filter: brightness(100%); }
                50% { transform: scale(1.008); filter: brightness(108%); }
            }
            @keyframes shadow-breathe {
                0%, 100% { box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
                50% { box-shadow: 0 15px 35px rgba(220,38,38,0.25); }
            }
            .animate-red-breathe {
                animation: soft-pulse-red 3s infinite ease-in-out;
            }
        </style>
        @foreach($domains as $domain)
            <div x-data="{ expanded: false, selectedTemplate: 'activation' }" 
                 class="bg-white rounded-[2rem] shadow-2xl border-2 border-gray-100 overflow-hidden hover:shadow-3xl hover:border-purple-300 transition-all duration-500 relative group {{ $domain->daysUntilExpiration() < 0 ? 'ring-4 ring-red-500/20 animate-[shadow-breathe_4s_infinite]' : '' }}">
                
                @php
                    $days = $domain->daysUntilExpiration();
                    $isExpired = $days < 0;
                    $isUrgent = $days >= 0 && $days <= 7;
                    
                    $headerBg = 'bg-indigo-50';
                    $textColor = 'text-indigo-950';
                    $subTextColor = 'text-indigo-800/70';
                    $badgeBg = 'bg-indigo-200';
                    $badgeText = 'text-indigo-900';

                    if ($isExpired) {
                        $headerBg = 'bg-rose-50';
                        $textColor = 'text-rose-950';
                        $subTextColor = 'text-rose-800/70';
                        $badgeBg = 'bg-rose-200';
                        $badgeText = 'text-rose-900';
                    } elseif ($isUrgent) {
                        $headerBg = 'bg-amber-50';
                        $textColor = 'text-amber-950';
                        $subTextColor = 'text-amber-800/70';
                        $badgeBg = 'bg-amber-200';
                        $badgeText = 'text-amber-900';
                    }
                @endphp

                <!-- Side Indicator for Urgency -->
                @if($isExpired)
                    <div class="absolute left-0 top-0 bottom-0 w-3 bg-red-700 z-10"></div>
                @elseif($isUrgent)
                    <div class="absolute left-0 top-0 bottom-0 w-3 bg-amber-600 z-10"></div>
                @endif

                <!-- Refined Header (Larger and better distributed) -->
                <div class="{{ $headerBg }} p-5 md:px-8 md:py-6 shadow-sm relative overflow-hidden transition-all duration-500 border-b border-black/5">
                    <div class="grid grid-cols-1 xl:grid-cols-[1fr_auto] items-center gap-6 md:gap-10 relative z-10">
                        
                        <!-- Left section: Main Info -->
                        <div class="flex-1 min-w-0 {{ $isExpired ? 'pl-4' : '' }}">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6">
                                <h3 class="text-xl md:text-2xl font-semibold {{ $textColor }} tracking-tight break-all">{{ $domain->domain_name }}</h3>
                                <div class="flex items-center gap-3">
                                    @php
                                        $style = match($domain->status) {
                                            'activo' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Activo'],
                                            'expirado' => [
                                                'bg' => 'bg-rose-200', 
                                                'text' => 'text-rose-900',
                                                'label' => abs($days) === 0 ? 'Expirado hoy' : 'Expirado hace ' . abs($days) . ' días'
                                            ],
                                            'pendiente' => ['bg' => 'bg-amber-200', 'text' => 'text-amber-900', 'label' => 'Pendiente'],
                                            default => ['bg' => 'bg-slate-200', 'text' => 'text-slate-900', 'label' => 'Suspendido']
                                        };
                                    @endphp
                                    <span class="px-5 py-2 rounded-full text-xs md:text-sm font-semibold {{ $style['bg'] }} {{ $style['text'] }} uppercase tracking-widest shadow-md border border-black/5">
                                        {{ $style['label'] }}
                                    </span>
                                </div>
                            </div>
                            <p class="{{ $subTextColor }} text-sm md:text-base font-semibold uppercase tracking-[0.15em] mt-2 flex items-center gap-2">
                                <span class="bg-black/5 px-4 py-1.5 rounded-xl backdrop-blur-sm border border-black/5">
                                    <span class="opacity-60 text-[10px] md:text-xs">CLIENTE:</span> {{ $domain->client_name ?? $domain->user?->name ?? 'Desconocido' }}
                                </span>
                            </p>
                        </div>

                        <!-- Right section: Action Panels -->
                        <div class="flex flex-wrap items-center gap-4 md:gap-6">
                            
                            <!-- Messages/WhatsApp (Box 1) -->
                            <div class="flex items-center bg-white/40 hover:bg-white/60 backdrop-blur-md rounded-[1.25rem] p-2 border-2 border-black/5 gap-3 transition-colors">
                                <select x-model="selectedTemplate" class="text-xs md:text-sm font-semibold {{ $textColor }} bg-transparent border-none focus:ring-0 cursor-pointer pr-10 py-3 appearance-none">
                                    <option value="activation" class="text-gray-900 font-bold">✅ Confirmación</option>
                                    <option value="expiry" class="text-gray-900 font-bold">📅 Vencimiento</option>
                                    <option value="promo" class="text-gray-900 font-bold">💰 Pago Pendiente</option>
                                    @if(isset($customMessages))
                                        @foreach($customMessages as $msg)
                                            <option value="custom_{{ $msg->id }}" class="text-gray-900 font-bold">✨ {{ $msg->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="flex items-center gap-4 px-3 border-l-2 border-black/10">
                                    <!-- Email Icon -->
                                    <button @click="$dispatch('open-email-modal', { domainId: {{ $domain->id }} })" 
                                            class="{{ $textColor }} opacity-70 hover:opacity-100 hover:scale-110 transition-all p-2" title="Enviar Email Masivo">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </button>

                                    <!-- Direct WhatsApp Icon -->
                                    @if($domain->phone)
                                        <button @click="$store.whatsapp.sendDirect('{{ $domain->phone }}', '{{ $domain->domain_name }}', '{{ $domain->client_name ?? 'Cliente' }}', selectedTemplate)" 
                                                class="text-green-600 opacity-80 hover:opacity-100 hover:scale-110 transition-all p-2" title="WhatsApp Rápido">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.008-3.568c0-3.639 2.964-6.6 6.603-6.6a6.56 6.56 0 0 1 6.603 6.6c0 3.639-2.964 6.6-6.603 6.6zm3.626-4.664c-.198-.099-1.171-.577-1.353-.643-.182-.066-.314-.099-.447.099-.133.198-.513.643-.627.775-.114.133-.232.148-.431.05-.198-.1-1.155-.425-2.201-1.36-.813-.725-1.361-1.62-1.521-1.818-.16-.198-.016-.305.087-.404.091-.088.198-.231.297-.346.099-.115.132-.198.198-.33.066-.133.033-.248-.016-.347-.05-.099-.447-1.077-.613-1.474-.162-.387-.329-.335-.447-.341-.115-.006-.247-.008-.378-.008s-.347.049-.529.248c-.182.198-.695.679-.695 1.655s.711 1.918.81 2.051c.099.133 1.396 2.133 3.38 2.993.472.203.841.325 1.13.417.476.151.909.129 1.25.078.381-.057 1.171-.478 1.336-.939.165-.461.165-.856.115-.939-.05-.083-.182-.133-.379-.232z"/></svg>
                                        </button>
                                    @endif

                                    <!-- Edit/Modal WhatsApp Icon -->
                                    <button @click="$dispatch('open-whatsapp-modal', { domainId: {{ $domain->id }}, domainName: '{{ $domain->domain_name }}', phone: '{{ $domain->phone }}', clientName: '{{ $domain->client_name ?? 'Cliente' }}', initialTemplate: selectedTemplate })" 
                                            class="{{ $textColor }} opacity-70 hover:opacity-100 hover:scale-110 transition-all p-2" title="Editar y Enviar WhatsApp">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Management Buttons (Box 2) -->
                            <div class="flex items-center gap-3">
                                <a href="{{ route('domains.edit', $domain) }}" class="p-4 bg-white/40 hover:bg-white text-indigo-700 hover:text-indigo-900 rounded-2xl transition-all shadow-sm active:scale-95 group/edit" title="Editar">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este dominio?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-4 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-2xl transition-all shadow-sm active:scale-95" title="Eliminar">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                                <button @click="expanded = !expanded" class="p-4 bg-white/20 hover:bg-white/40 rounded-2xl {{ $textColor }} transition-all shadow-sm active:scale-95">
                                    <svg class="w-6 h-6 transform transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </div>

                            <!-- Time Status (Box 3) -->
                            <div class="px-6 py-4 bg-white/50 rounded-2xl border-2 border-black/5 min-w-[200px] text-center shadow-lg backdrop-blur-xl">
                                @if($isExpired)
                                    <span class="text-xs font-semibold {{ $textColor }} uppercase tracking-[0.25em] flex items-center justify-center gap-2">
                                        <span class="w-2 h-2 {{ $badgeBg }} rounded-full animate-ping"></span>
                                        ¡EXPIRADO!
                                    </span>
                                @elseif($isUrgent)
                                    <span class="text-xs font-semibold {{ $textColor }} uppercase tracking-[0.1em]">Vence en {{ $days }} días</span>
                                @else
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="w-5 h-5 {{ $subTextColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="text-xs font-semibold {{ $textColor }} opacity-95 uppercase tracking-widest">Expira: {{ $days }} días</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Body (Expanded) -->
                <div x-show="expanded" x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-[1000px]"
                     class="p-10 bg-slate-50 relative">
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 hover:border-purple-200 transition-colors">
                            <span class="text-xs font-black text-slate-400 uppercase mb-3 block tracking-widest">📞 Teléfono / WhatsApp</span>
                            <p class="text-lg font-black text-slate-800">{{ $domain->phone ?: 'No registrado' }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 hover:border-emerald-200 transition-colors">
                            <span class="text-xs font-black text-slate-400 uppercase mb-3 block tracking-widest">📅 Fecha Activación</span>
                            <p class="text-lg font-black text-slate-700">{{ $domain->registration_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 hover:border-amber-200 transition-colors">
                            <span class="text-xs font-black text-slate-400 uppercase mb-3 block tracking-widest">⏳ Fecha Vencimiento</span>
                            <p class="text-lg font-black text-slate-700">{{ $domain->expiration_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate- 200 hover:border-purple-400 transition-colors bg-gradient-to-br from-purple-50/50 to-white">
                            <span class="text-xs font-black text-purple-600 uppercase mb-3 block tracking-widest">💰 Inversión Renovación</span>
                            <p class="text-2xl font-black text-purple-900">S/ {{ number_format($domain->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-10">{{ $domains->links() }}</div>
@else
    <div class="bg-white rounded-[2.5rem] shadow-xl p-20 text-center border-4 border-dashed border-slate-100 mt-10">
        <div class="mb-6 flex justify-center">
            <div class="p-6 bg-slate-50 rounded-full">
                <svg class="h-20 w-20 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
            </div>
        </div>
        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Tu panel está vacío</h3>
        <p class="mt-4 text-slate-500 font-medium">Comienza a centralizar la gestión de tus dominios ahora mismo.</p>
        <a href="{{ route('domains.create') }}" class="mt-10 inline-flex items-center px-10 py-5 bg-[#5F1BF2] hover:bg-[#4c16c2] text-white rounded-2xl font-black uppercase text-sm tracking-[0.2em] shadow-2xl transition-all hover:scale-105 active:scale-95">
            + Nuevo Dominio
        </a>
    </div>
@endif
