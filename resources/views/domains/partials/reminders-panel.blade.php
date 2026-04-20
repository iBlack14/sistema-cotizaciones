<div x-data="{
    reminders: [],
    loading: true,
    init() {
        fetch('{{ route('api.reminders') }}')
            .then(res => res.json())
            .then(data => {
                this.reminders = data;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
    }
}">
    <div class="overflow-hidden rounded-2xl border border-white/50 bg-white/90 shadow-xl backdrop-blur-xl">
        <div class="border-b border-slate-200/80 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#5F1BF2] text-white shadow-lg">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Recordatorios</h3>
                    <p class="text-sm text-slate-500">Seguimiento del dia</p>
                </div>
            </div>
        </div>

        <div class="max-h-[32rem] overflow-y-auto">
            <div x-show="loading" class="px-5 py-10 text-center">
                <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-[#F2059F]/20 border-t-[#8704BF]"></div>
                <p class="mt-3 text-sm font-medium text-slate-500">Cargando recordatorios...</p>
            </div>

            <template x-for="reminder in reminders" :key="reminder.id">
                <div class="border-b border-slate-100 px-5 py-4 last:border-b-0">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl bg-[#BF1F6A] text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900" x-text="reminder.client"></p>
                            <p class="mt-1 text-sm text-slate-500" x-text="reminder.reason"></p>
                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold">
                                <a :href="`https://wa.me/${reminder.phone}`" target="_blank" class="text-green-600 transition hover:text-green-500">
                                    WhatsApp
                                </a>
                                <a :href="reminder.link" target="_blank" class="text-[#BF1F6A] transition hover:text-[#8704BF]">
                                    Ver cotizacion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!loading && reminders.length === 0" class="px-5 py-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">No hay recordatorios para hoy.</p>
            </div>
        </div>
    </div>
</div>
