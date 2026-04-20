<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Notas') }}
            </h2>
            <p class="text-sm text-white/80 mt-1">
                {{ $headerDate ?? now()->format('d/m/Y') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto lg:px-8" x-data="notesPage(@js($services ?? []), @js($savedNotes ?? []))">
            <form method="POST" action="{{ route('quotations.notes.export-pdf') }}">
                @csrf

                <div class="bg-white/90 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/50 relative">
                    <div
                        class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F]">
                    </div>

                    <div class="p-8 space-y-6 text-gray-800">
                        <div class="flex items-center justify-end gap-3">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full border border-white/70"
                                    :class="{
                                        'bg-green-500 shadow-[0_0_0_3px_rgba(34,197,94,0.18)]': saveState === 'saved',
                                        'bg-amber-400 shadow-[0_0_0_3px_rgba(251,191,36,0.18)] animate-pulse': saveState === 'saving',
                                        'bg-red-500 shadow-[0_0_0_3px_rgba(239,68,68,0.18)]': saveState === 'error'
                                    }"
                                    title="Estado de guardado automático"></span>
                                <button type="button" @click="addNote()"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] border-0 rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BF1F6A] transition ease-in-out duration-150">
                                    + Agregar Nota
                                </button>
                                <select name="export_month"
                                    class="block w-44 bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm text-sm">
                                    <option value="">Todos los meses</option>
                                    <template x-for="m in months" :key="'exp-' + m">
                                        <option :value="m" x-text="'Mes: ' + m"></option>
                                    </template>
                                </select>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2 bg-vc-magenta hover:bg-fuchsia-600 border-0 rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-md shadow-vc-magenta/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-vc-magenta transition ease-in-out duration-150">
                                    Exportar PDF
                                </button>
                                <button type="submit" formaction="{{ route('quotations.notes.export-word') }}"
                                    class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 border-0 rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-md shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    Exportar Word
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" @input="scheduleAutoSave()" @change="scheduleAutoSave()">
                            <template x-for="(note, index) in notes" :key="note.uid">
                                <div x-show="!note.collapsed" x-transition.opacity
                                    class="rounded-2xl border border-[#8704BF]/20 bg-white p-5 h-full relative overflow-visible">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-800" x-text="'Nota #' + (index + 1)"></h3>
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                @click="note.pinSide = 'left'; note.collapsed = true; scheduleAutoSave()"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5F1BF2] hover:border-[#5F1BF2]/40">
                                                Izq
                                            </button>
                                            <button type="button"
                                                @click="note.pinSide = 'right'; note.collapsed = true; scheduleAutoSave()"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5F1BF2] hover:border-[#5F1BF2]/40">
                                                Der
                                            </button>
                                            <a :href="whatsappLink(note)" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-[#25D366] to-[#128C7E] rounded-lg text-xs font-semibold text-white uppercase tracking-wider">
                                                WhatsApp
                                            </a>
                                            <button type="button" @click="removeNote(index)" x-show="notes.length > 1"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:text-red-600 hover:border-red-300">
                                                Quitar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="hidden" :name="'notes[' + index + '][id]'" x-model="note.id">
                                        <div class="md:col-span-2">
                                            <x-input-label :value="__('Llamar (dia y mes)')" class="text-gray-700" />
                                            <div class="grid grid-cols-2 gap-3 mt-1">
                                                <select x-model="note.llamarDay" @change="syncLlamar(note)"
                                                    class="block w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                    <option value="">Dia</option>
                                                    <template x-for="d in 31" :key="d">
                                                        <option :value="String(d)" :selected="note.llamarDay === String(d)" x-text="d"></option>
                                                    </template>
                                                </select>
                                                <select x-model="note.llamarMonth" @change="syncLlamar(note)"
                                                    class="block w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                    <option value="">Mes</option>
                                                    <template x-for="m in months" :key="m">
                                                        <option :value="m" :selected="note.llamarMonth === m" x-text="m"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <input type="hidden" x-model="note.llamar" :name="'notes[' + index + '][llamar]'">
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-input-label :value="__('RUC')" class="text-gray-700" />
                                            <input type="text" x-model="note.ruc" :name="'notes[' + index + '][ruc]'"
                                                class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                                placeholder="Número de RUC">
                                        </div>

                                        <div>
                                            <x-input-label :value="__('Cliente')" class="text-gray-700" />
                                            <input type="text" x-model="note.cliente" :name="'notes[' + index + '][cliente]'"
                                                class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                                placeholder="Nombre del cliente">
                                        </div>

                                        <div>
                                            <x-input-label :value="__('Telefono')" class="text-gray-700" />
                                            <input type="text" x-model="note.telefono" :name="'notes[' + index + '][telefono]'"
                                                class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                                placeholder="51999999999">
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-input-label :value="__('Servicio')" class="text-gray-700" />
                                            <select x-model="note.servicio" :name="'notes[' + index + '][servicio]'"
                                                class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                <option value="">Seleccione un servicio...</option>
                                                <template x-for="service in services" :key="service">
                                                    <option :value="service" :selected="note.servicio === service" x-text="service"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-input-label :value="__('Descripcion')" class="text-gray-700" />
                                            <textarea x-model="note.descripcion" :name="'notes[' + index + '][descripcion]'" rows="4"
                                                class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm"
                                                placeholder="Detalle de la nota..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>
            </form>

            <div class="hidden lg:flex fixed left-2 top-1/2 -translate-y-1/2 z-30 flex-col gap-2 pointer-events-none">
                <template x-for="note in collapsedNotes('left')" :key="'left-' + note.uid">
                    <button type="button" @click="note.collapsed = false; scheduleAutoSave()"
                        class="pointer-events-auto px-3 py-2 rounded-r-lg bg-white/95 text-[#4C1D95] text-xs font-semibold border border-[#C4B5FD] shadow-[0_6px_18px_rgba(30,10,70,0.30)] max-w-[170px] truncate text-left hover:bg-white">
                        <span x-text="note.cliente || 'Sin cliente'"></span>
                    </button>
                </template>
            </div>

            <div class="hidden lg:flex fixed right-2 top-1/2 -translate-y-1/2 z-30 flex-col gap-2 pointer-events-none items-end">
                <template x-for="note in collapsedNotes('right')" :key="'right-' + note.uid">
                    <button type="button" @click="note.collapsed = false; scheduleAutoSave()"
                        class="pointer-events-auto px-3 py-2 rounded-l-lg bg-[#FEF3C7] text-[#7C2D12] text-xs font-semibold border border-[#FCD34D] shadow-[0_6px_18px_rgba(30,10,70,0.30)] max-w-[170px] truncate text-right hover:bg-[#FFFBEB]">
                        <span x-text="note.cliente || 'Sin cliente'"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <script>
        function notesPage(services, savedNotes) {
            return {
                services: services || [],
                months: ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
                notes: Array.isArray(savedNotes) && savedNotes.length
                    ? savedNotes.map((note) => createNote(note))
                    : [createNote()],
                saveTimer: null,
                saveState: 'saved',
                isSaving: false,
                pendingSave: false,
                readyForAutosave: false,
                init() {
                    this.$nextTick(() => {
                        this.readyForAutosave = true;
                    });
                },
                addNote() {
                    this.notes.push(createNote());
                    this.scheduleAutoSave();
                },
                removeNote(index) {
                    this.notes.splice(index, 1);
                    this.scheduleAutoSave();
                },
                collapsedNotes(side) {
                    return this.notes.filter((note) => note.collapsed && note.pinSide === side);
                },
                syncLlamar(note) {
                    if (!note) return;
                    if (note.llamarDay && note.llamarMonth) {
                        note.llamar = `${note.llamarDay} ${note.llamarMonth}`;
                    } else if (!note.llamarDay && !note.llamarMonth) {
                        note.llamar = '';
                    }
                    this.scheduleAutoSave(true);
                },
                scheduleAutoSave(immediate = false) {
                    if (!this.readyForAutosave) {
                        return;
                    }
                    this.saveState = 'saving';
                    if (this.saveTimer) {
                        clearTimeout(this.saveTimer);
                    }

                    if (immediate) {
                        this.saveTimer = null;
                        this.flushAutoSave();
                        return;
                    }

                    this.saveTimer = setTimeout(() => this.flushAutoSave(), 700);
                },
                flushAutoSave() {
                    if (this.isSaving) {
                        this.pendingSave = true;
                        return;
                    }
                    this.autoSave();
                },
                async autoSave() {
                    this.isSaving = true;
                    try {
                        const response = await fetch('{{ route("quotations.notes.save") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                notes: this.notes.map((note) => ({
                                    ...this.buildLlamarPayload(note),
                                    id: note.id,
                                    local_key: note.uid,
                                    cliente: note.cliente,
                                    ruc: note.ruc,
                                    telefono: note.telefono,
                                    servicio: note.servicio,
                                    descripcion: note.descripcion,
                                    collapsed: !!note.collapsed,
                                    pin_side: note.pinSide === 'left' ? 'left' : 'right'
                                }))
                            })
                        });

                        if (!response.ok) {
                            this.saveState = 'error';
                            return;
                        }

                        const data = await response.json();
                        if (data && Array.isArray(data.mappings)) {
                            const idByLocalKey = Object.fromEntries(
                                data.mappings.map((item) => [item.local_key, item.id])
                            );
                            this.notes.forEach((note) => {
                                if (idByLocalKey[note.uid]) {
                                    note.id = idByLocalKey[note.uid];
                                }
                            });
                        }
                        this.saveState = 'saved';
                    } catch (e) {
                        this.saveState = 'error';
                    } finally {
                        this.isSaving = false;
                        if (this.pendingSave) {
                            this.pendingSave = false;
                            this.flushAutoSave();
                        }
                    }
                },
                buildLlamarPayload(note) {
                    const parsed = parseLlamarParts(note.llamar || '');
                    const day = note.llamarDay || parsed.day || '';
                    const month = note.llamarMonth || parsed.month || '';
                    const llamar = (day && month) ? `${day} ${month}` : (note.llamar || '');

                    return {
                        llamar,
                        llamar_day: day,
                        llamar_month: month
                    };
                },
                whatsappLink(note) {
                    const phone = String(note.telefono || '').replace(/\D/g, '');
                    const message = [
                        `Llamar: ${note.llamar || '-'}`,
                        `RUC: ${note.ruc || '-'}`,
                        `Cliente: ${note.cliente || '-'}`,
                        `Telefono: ${note.telefono || '-'}`,
                        `Servicio: ${note.servicio || '-'}`,
                        `Descripcion: ${note.descripcion || '-'}`
                    ].join('\n');

                    if (!phone) {
                        return `https://wa.me/?text=${encodeURIComponent(message)}`;
                    }

                    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                }
            };
        }

        function parseLlamarParts(value) {
            const raw = String(value || '').trim().toLowerCase();
            const match = raw.match(/(\d{1,2})\s*(?:de\s+)?(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)/i);
            if (!match) {
                return { day: '', month: '' };
            }

            const dayNumber = Number.parseInt(match[1], 10);
            const day = Number.isFinite(dayNumber) && dayNumber >= 1 && dayNumber <= 31 ? String(dayNumber) : '';
            const month = String(match[2]).toLowerCase().replace('setiembre', 'septiembre');

            return { day, month };
        }

        function createNote(initial = {}) {
            const llamarRaw = String(initial.llamar ?? '').trim();
            const parsed = parseLlamarParts(llamarRaw);
            const note = {
                uid: initial.uid ?? (initial.id ? ('db-' + initial.id) : (Date.now().toString(36) + Math.random().toString(36).slice(2))),
                id: initial.id ?? '',
                cliente: initial.cliente ?? '',
                ruc: initial.ruc ?? '',
                telefono: initial.telefono ?? '',
                llamar: llamarRaw,
                llamarDay: parsed.day,
                llamarMonth: parsed.month.toLowerCase(),
                servicio: initial.servicio ?? '',
                descripcion: initial.descripcion ?? '',
                collapsed: !!initial.collapsed,
                pinSide: initial.pin_side === 'left' ? 'left' : 'right'
            };
            return note;
        }
    </script>
</x-app-layout>
