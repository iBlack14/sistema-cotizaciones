<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Editar Dominio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white/10 border border-white/20 rounded-2xl p-6 shadow-xl shadow-[#5F1BF2]/20 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Dominios</p>
                <h1 class="text-3xl font-bold text-white mt-2">Editar Dominio</h1>
                <p class="text-white/80 text-sm mt-2">{{ $domain->domain_name }}</p>
            </div>

            <div class="relative">
                <div class="absolute -top-6 -left-10 h-32 w-32 bg-gradient-to-br from-[#5F1BF2] to-[#F2059F] blur-3xl opacity-40 pointer-events-none"></div>
                <div class="absolute -bottom-12 -right-6 h-36 w-36 bg-gradient-to-br from-[#8704BF] to-[#BF1F6A] blur-3xl opacity-35 pointer-events-none"></div>

                <div class="bg-white/90 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/50 relative">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F]"></div>
                    <div class="p-8 text-gray-800 space-y-8">
                        <form method="POST" action="{{ route('domains.update', $domain) }}">
                            @csrf
                            @method('PUT')

                            <!-- Domain Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 bg-white/60 border border-[#F2059F]/15 rounded-2xl p-6 shadow-inner shadow-white/40">
                                <div class="md:col-span-2">
                                    <x-input-label for="domain_name" :value="__('Nombre del Dominio')" class="text-gray-700" />
                                    <x-text-input id="domain_name" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl" type="text" name="domain_name" :value="old('domain_name', $domain->domain_name)" placeholder="ejemplo.com" required />
                                    <x-input-error :messages="$errors->get('domain_name')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="client_name" :value="__('Cliente')" class="text-gray-700" />
                                    <input 
                                        id="client_name" 
                                        name="client_name" 
                                        type="text"
                                        value="{{ old('client_name', $domain->client_name ?? $domain->user?->name) }}"
                                        placeholder="Escribe el nombre del cliente..."
                                        class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="registration_date" :value="__('Fecha de Registro')" class="text-gray-700" />
                                    <x-text-input id="registration_date" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl" type="date" name="registration_date" :value="old('registration_date', $domain->registration_date->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('registration_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="expiration_date" :value="__('Fecha de Expiración')" class="text-gray-700" />
                                    <x-text-input id="expiration_date" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl" type="date" name="expiration_date" :value="old('expiration_date', $domain->expiration_date->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="price" :value="__('Precio Anual (S/)')" class="text-gray-700" />
                                    <x-text-input id="price" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl" type="number" name="price" :value="old('price', $domain->price)" step="0.01" min="0" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status" :value="__('Estado')" class="text-gray-700" />
                                    <select id="status" name="status" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" required>
                                        <option value="activo" {{ old('status', $domain->status) === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="pendiente" {{ old('status', $domain->status) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="suspendido" {{ old('status', $domain->status) === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="expirado" {{ old('status', $domain->status) === 'expirado' ? 'selected' : '' }}>Expirado</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="auto_renew" value="1" {{ old('auto_renew', $domain->auto_renew) ? 'checked' : '' }} class="rounded border-gray-300 text-vc-magenta shadow-sm focus:ring-vc-magenta">
                                        <span class="ml-2 text-sm text-gray-700 font-medium">Auto-renovación activada</span>
                                    </label>
                                </div>

                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <x-input-label for="phone" :value="__('Teléfono / WhatsApp')" class="text-gray-700" />
                                        <x-text-input id="phone" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl" type="text" name="phone" :value="old('phone', $domain->phone)" placeholder="999888777" />
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="corporate_emails" :value="__('Correos Corporativos')" class="text-gray-700" />
                                        <textarea id="corporate_emails" name="corporate_emails" rows="3" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="info@empresa.com">{{ old('corporate_emails', $domain->corporate_emails) }}</textarea>
                                        <x-input-error :messages="$errors->get('corporate_emails')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="emails" :value="__('Correos Personales')" class="text-gray-700" />
                                        <textarea id="emails" name="emails" rows="3" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="usuario@gmail.com">{{ old('emails', $domain->emails) }}</textarea>
                                        <x-input-error :messages="$errors->get('emails')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="hosting_info" :value="__('Información de Hosting (Opcional)')" class="text-gray-700" />
                                    <textarea id="hosting_info" name="hosting_info" rows="2" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="Proveedor de hosting, plan, etc.">{{ old('hosting_info', $domain->hosting_info) }}</textarea>
                                    <x-input-error :messages="$errors->get('hosting_info')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="dns_servers" :value="__('Servidores DNS (Opcional)')" class="text-gray-700" />
                                    <textarea id="dns_servers" name="dns_servers" rows="2" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="ns1.example.com, ns2.example.com">{{ old('dns_servers', $domain->dns_servers) }}</textarea>
                                    <x-input-error :messages="$errors->get('dns_servers')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="plugins" :value="__('Plugins Instalados (Opcional)')" class="text-gray-700" />
                                    <textarea id="plugins" name="plugins" rows="2" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="WooCommerce, Elementor, Yoast SEO, etc.">{{ old('plugins', $domain->plugins) }}</textarea>
                                    <x-input-error :messages="$errors->get('plugins')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="licenses" :value="__('Licencias Activas (Opcional)')" class="text-gray-700" />
                                    <textarea id="licenses" name="licenses" rows="2" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="Antivirus activo, SSL activo, Premium activa, etc.">{{ old('licenses', $domain->licenses) }}</textarea>
                                    <x-input-error :messages="$errors->get('licenses')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="maintenance_status" :value="__('Servicio de Mantenimiento')" class="text-gray-700" />
                                    <select id="maintenance_status" name="maintenance_status" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm">
                                        <option value="activo" {{ old('maintenance_status', $domain->maintenance_status) === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('maintenance_status', $domain->maintenance_status) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('maintenance_status')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="notes" :value="__('Notas Internas (Opcional)')" class="text-gray-700" />
                                    <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full bg-white border-gray-200 text-gray-900 placeholder-gray-400 focus:border-vc-magenta focus:ring-vc-magenta rounded-xl shadow-sm" placeholder="Notas adicionales sobre este dominio...">{{ old('notes', $domain->notes) }}</textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <a href="{{ route('domains.index') }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancelar
                                </a>
                                <x-primary-button class="bg-vc-magenta hover:bg-fuchsia-600 focus:bg-fuchsia-600 active:bg-fuchsia-700 border-0 shadow-lg shadow-vc-magenta/30">
                                    {{ __('Actualizar Dominio') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
