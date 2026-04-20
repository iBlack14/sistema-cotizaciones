<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div
                class="relative overflow-hidden p-6 sm:p-8 bg-white/10 border border-white/20 rounded-2xl shadow-xl shadow-[#5F1BF2]/20 backdrop-blur">
                <div
                    class="absolute -top-10 -right-8 h-36 w-36 bg-gradient-to-br from-[#5F1BF2] to-[#F2059F] blur-3xl opacity-35 pointer-events-none">
                </div>
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Cuenta</p>
                <h1 class="text-3xl font-bold text-white mt-2">Configuracion de Perfil</h1>
                <p class="text-white/80 text-sm mt-2">Actualiza tus datos, cambia tu contrasena y administra tu cuenta.</p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div
                    class="bg-white/90 backdrop-blur-xl border border-white/60 shadow-2xl shadow-[#8704BF]/10 rounded-2xl overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F]"></div>
                    <div class="p-5 sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div
                    class="bg-white/90 backdrop-blur-xl border border-white/60 shadow-2xl shadow-[#8704BF]/10 rounded-2xl overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F]"></div>
                    <div class="p-5 sm:p-8">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div
                class="bg-white/90 backdrop-blur-xl border border-white/60 shadow-2xl shadow-[#8704BF]/10 rounded-2xl overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-[#BF1F6A] via-[#F2059F] to-[#8704BF]"></div>
                <div class="p-5 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
