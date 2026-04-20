<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Nueva Cotizacion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto lg:px-8 space-y-8" x-data="quotationForm()">
            <div class="bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl shadow-[#5F1BF2]/20 backdrop-blur">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-white/70">Cotizaciones</p>
                        <h1 class="text-3xl font-bold text-white mt-2">Nueva Cotizacion</h1>
                        <p class="text-white/80 text-sm mt-2">Completa los datos del cliente y arma tu propuesta con el esquema
                            violeta-fucsia.</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div
                    class="absolute -top-6 -left-10 h-32 w-32 bg-gradient-to-br from-[#5F1BF2] to-[#F2059F] blur-3xl opacity-40 pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-12 -right-6 h-36 w-36 bg-gradient-to-br from-[#8704BF] to-[#BF1F6A] blur-3xl opacity-35 pointer-events-none">
                </div>

                <div class="bg-white/90 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/50 relative">
                    <div
                        class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F]">
                    </div>
                    <div class="p-8 text-gray-800 space-y-8">
                        <form method="POST" action="{{ route('quotations.store') }}" target="_blank">
                            @csrf
                            <!-- Client Data -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2 bg-white/60 border border-[#F2059F]/15 rounded-2xl p-6 shadow-inner shadow-white/40">
                                <div>
                                    <x-input-label for="date" :value="__('Fecha')" class="text-gray-700" />
                                    <x-text-input id="date"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="date" name="date" :value="date('Y-m-d')" />
                                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="client_company" :value="__('Empresa / Razon Social')"
                                        class="text-gray-700" />
                                    <x-text-input id="client_company"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="text" name="client_company" />
                                    <x-input-error :messages="$errors->get('client_company')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="client_ruc" :value="__('RUC')" class="text-gray-700" />
                                    <x-text-input id="client_ruc"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="text" name="client_ruc" />
                                    <x-input-error :messages="$errors->get('client_ruc')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="client_phone" :value="__('Telefono')" class="text-gray-700" />
                                    <x-text-input id="client_phone"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="text" name="client_phone" />
                                    <x-input-error :messages="$errors->get('client_phone')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="client_email" :value="__('Correo Electronico')"
                                        class="text-gray-700" />
                                    <x-text-input id="client_email"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="email" name="client_email" />
                                    <x-input-error :messages="$errors->get('client_email')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="client_address" :value="__('Direccion')"
                                        class="text-gray-700" />
                                    <x-text-input id="client_address"
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl"
                                        type="text" name="client_address" />
                                    <x-input-error :messages="$errors->get('client_address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Items -->
                            <div
                                class="bg-white/80 rounded-2xl border border-[#5F1BF2]/15 shadow-md shadow-[#8704BF]/10">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 px-6 pt-6">Servicios / Productos
                                </h3>
                                <div class="overflow-x-auto rounded-2xl border border-white/60">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead
                                            class="bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F] text-white">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                                    Servicio / Producto</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-24">
                                                    Cantidad</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-32">
                                                    Precio</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-32">
                                                    Total</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-20 text-center">
                                                    Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white/60">
                                            <template x-for="(item, index) in items" :key="index">
                                                <tr class="hover:text-white transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap overflow-visible relative">
                                                        <div x-data="{ 
                                                            isCustom: false, 
                                                            dropdownOpen: false,
                                                            dropdownStyle: '',
                                                            services: @js($services ?? []),
                                                            selectService(service) {
                                                                if(service === 'OTRO') {
                                                                    this.isCustom = true;
                                                                    item.service_name = '';
                                                                } else {
                                                                    item.service_name = service;
                                                                }
                                                                this.dropdownOpen = false;
                                                            },
                                                            updateDropdownPosition() {
                                                                const rect = this.$refs.trigger.getBoundingClientRect();
                                                                const vw = window.innerWidth;
                                                                const menuWidth = rect.width;
                                                                let left = rect.left;
                                                                let top = rect.bottom + 8;
                                                                if (left + menuWidth > vw - 8) {
                                                                    left = Math.max(8, vw - menuWidth - 8);
                                                                }
                                                                this.dropdownStyle = `top:${top}px; left:${left}px; width:${menuWidth}px;`;
                                                            }
                                                        }"
                                                            x-init="isCustom = !services.includes(item.service_name) && item.service_name !== ''">

                                                            <!-- Native Select Dropdown -->
                                                            <div x-show="!isCustom">
                                                                <select x-model="item.service_name"
                                                                    @change="if($event.target.value === 'OTRO') selectService('OTRO')"
                                                                    class="block w-full bg-white border-2 border-[#F2059F] text-gray-900 focus:border-[#F2059F] focus:ring-2 focus:ring-[#F2059F] rounded-xl shadow-sm px-4 py-2.5">
                                                                    <option value="" disabled selected>Seleccione...
                                                                    </option>
                                                                    <template x-for="service in services"
                                                                        :key="service">
                                                                        <option :value="service" x-text="service">
                                                                        </option>
                                                                    </template>
                                                                    <option value="OTRO">OTRO (Especificar)</option>
                                                                </select>

                                                                <!-- Hidden input to submit the value (if needed for backend compatibility, though select provides value) -->
                                                                <!-- If select is used for name attribute directly, we can remove hidden input if x-model handles it. 
                                                                     However, the original code used hidden input with name attribute. 
                                                                     Here we can put the name attribute on the select directly if it's not custom, 
                                                                     or keep hidden input. Let's put name on select but remove it when custom is active to avoid duplicate names if necessary, 
                                                                     or just rely on the hidden input strategy if that's safer. 
                                                                     Actually, best to use the name on select directly when !isCustom. -->
                                                                <input x-show="!isCustom" type="hidden"
                                                                    :name="'items['+index+'][service_name]'"
                                                                    x-model="item.service_name">
                                                            </div>

                                                            <!-- Custom Input -->
                                                            <div x-show="isCustom" class="space-y-3">
                                                                <div class="flex gap-2">
                                                                    <input type="text"
                                                                        :name="'items['+index+'][service_name]'"
                                                                        x-model="item.service_name"
                                                                        placeholder="Descripcion del servicio"
                                                                        class="block w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                                    <button type="button"
                                                                        @click="isCustom = false; item.service_name = ''"
                                                                        class="text-xs text-[#8704BF] hover:text-[#F2059F] underline whitespace-nowrap">Volver
                                                                        a lista</button>
                                                                </div>

                                                                <!-- Image Gallery Selector -->
                                                                <div
                                                                    class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                                    <div class="flex justify-between items-center mb-2">
                                                                        <label
                                                                            class="block text-sm font-medium text-gray-700">
                                                                            <svg class="inline w-4 h-4 mr-1" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                            </svg>
                                                                            Imagen del Servicio
                                                                        </label>
                                                                        <button type="button"
                                                                            @click="item.showGallery = !item.showGallery"
                                                                            class="text-xs px-3 py-1 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] text-white rounded-lg hover:opacity-90">
                                                                            <span
                                                                                x-text="item.showGallery ? 'Ocultar' : 'Seleccionar'"></span>
                                                                        </button>
                                                                    </div>

                                                                    <!-- Hidden input SIEMPRE presente -->
                                                                    <input type="hidden"
                                                                        :name="'items['+index+'][image_path]'"
                                                                        :value="item.selectedImage || ''">

                                                                    <!-- Image Preview - shows logo.png as placeholder -->
                                                                    <div
                                                                        class="mb-3 bg-white rounded-lg p-2 border border-gray-200">
                                                                        <img :src="item.selectedImage || '{{ asset('images/logo.png') }}'"
                                                                            class="max-h-24 mx-auto rounded-lg border-2 shadow-sm"
                                                                            :class="item.selectedImage ? 'border-[#8704BF] object-cover' : 'border-gray-300 opacity-40 object-contain'"
                                                                            style="max-width: 100%;">
                                                                        <p x-show="!item.selectedImage"
                                                                            class="text-xs text-gray-500 mt-1 text-center">
                                                                            Vista previa - Selecciona una imagen</p>
                                                                    </div>

                                                                    <!-- Gallery Grid -->
                                                                    <div x-show="item.showGallery" x-transition
                                                                        class="grid grid-cols-3 gap-2 mt-2 max-h-64 overflow-y-auto p-2 bg-white rounded-lg">
                                                                        @forelse($images as $image)
                                                                            <div @click="item.selectedImage = '{{ $image->url }}'; item.showGallery = false"
                                                                                class="cursor-pointer group relative rounded-lg overflow-hidden border-2 hover:border-[#F2059F] transition-all"
                                                                                :class="item.selectedImage === '{{ $image->url }}' ? 'border-[#8704BF] ring-2 ring-[#8704BF]' : 'border-gray-200'">
                                                                                <img src="{{ $image->url }}"
                                                                                    alt="{{ $image->name }}"
                                                                                    class="w-full h-20 object-cover group-hover:scale-110 transition-transform">
                                                                                <div
                                                                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-1">
                                                                                    <span
                                                                                        class="text-white text-[10px] font-medium truncate">{{ $image->name }}</span>
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <div
                                                                                class="col-span-3 text-center text-xs text-gray-500 py-4">
                                                                                No hay imágenes disponibles. Agrega nuevas
                                                                                en Ajustes.
                                                                            </div>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="number" :name="'items['+index+'][quantity]'"
                                                            x-model="item.quantity" min="1"
                                                            class="block w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="relative">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                                                            <input type="number" :name="'items['+index+'][price]'"
                                                                x-model="item.price" min="0" step="0.01"
                                                                class="block w-full pl-10 bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <span x-text="'S/ ' + (item.quantity * item.price).toFixed(2)"
                                                            class="text-gray-900 font-semibold"></span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <button type="button" @click="removeItem(index)"
                                                            class="text-[#BF1F6A] hover:text-[#F2059F] transition-colors">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 border-t border-gray-100 bg-white/70">
                                                    <button type="button" @click="addItem()"
                                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#5F1BF2] to-[#F2059F] border-0 rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-md shadow-[#8704BF]/20 hover:shadow-lg hover:shadow-[#8704BF]/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BF1F6A] focus:ring-offset-white transition ease-in-out duration-150">
                                                        + Agregar Item
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Totals --><!-- Totals -->
                            <div class="mt-6 flex justify-end">
                                <div class="w-64 bg-white/50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between mb-2">
                                        <span class="font-medium text-gray-600">Subtotal:</span>
                                        <span class="text-gray-800" x-text="'S/ ' + subtotal.toFixed(2)"></span>
                                    </div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="apply_igv" value="1" x-model="applyIgv"
                                                class="rounded border-gray-300 text-vc-magenta shadow-sm focus:ring-vc-magenta">
                                            <span class="ml-2 text-sm text-gray-600">IGV (18%)</span>
                                        </label>
                                        <span class="text-gray-800" x-text="'S/ ' + igvAmount.toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                        <span class="font-bold text-gray-800 text-lg">Total:</span>
                                        <span class="font-bold text-vc-magenta text-lg"
                                            x-text="'S/ ' + total.toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-6">
                                <x-primary-button
                                    class="bg-vc-magenta hover:bg-fuchsia-600 focus:bg-fuchsia-600 active:bg-fuchsia-700 border-0 shadow-lg shadow-vc-magenta/30">
                                    {{ __('Generar Cotizacion') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function quotationForm() {
                return {
                    items: [
                        { service_name: '', quantity: 1, price: 0, selectedImage: null, showGallery: false }
                    ],
                    applyIgv: false,
                    addItem() {
                        this.items.push({ service_name: '', quantity: 1, price: 0, selectedImage: null, showGallery: false });
                    },
                    removeItem(index) {
                        this.items.splice(index, 1);
                    },
                    get subtotal() {
                        return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    },
                    get igvAmount() {
                        return this.applyIgv ? this.subtotal * 0.18 : 0;
                    },
                    get total() {
                        return this.subtotal + this.igvAmount;
                    }
                }
            }
        </script>
</x-app-layout>
