<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Mensaje') }}
            </h2>
            <a href="{{ route('messages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('messages.store') }}" x-data="messageForm()">
                        @csrf
                        
                        <!-- Tipo de destinatario -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Destinatario</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="recipient_type" value="custom" 
                                           x-model="recipientType" 
                                           class="form-radio text-indigo-600" 
                                           {{ $recipientType === 'custom' ? 'checked' : '' }}>
                                    <span class="ml-2">Personalizado</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="recipient_type" value="domains" 
                                           x-model="recipientType" 
                                           class="form-radio text-indigo-600"
                                           {{ $recipientType === 'domains' ? 'checked' : '' }}>
                                    <span class="ml-2">Dominios</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="recipient_type" value="quotations" 
                                           x-model="recipientType" 
                                           class="form-radio text-indigo-600"
                                           {{ $recipientType === 'quotations' ? 'checked' : '' }}>
                                    <span class="ml-2">Cotizaciones</span>
                                </label>
                            </div>
                        </div>

                        <!-- Asunto -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700">Asunto</label>
                            <input type="text" name="subject" id="subject" 
                                   value="{{ old('subject', $template ? $template->title : '') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-400" 
                                   required>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo y Prioridad -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select name="type" id="type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" 
                                        required>
                                    <option value="email" {{ old('type', $type) === 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ old('type', $type) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="sms" {{ old('type', $type) === 'sms' ? 'selected' : '' }}>SMS</option>
                                    <option value="notification" {{ old('type', $type) === 'notification' ? 'selected' : '' }}>Notificación</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
                                <select name="priority" id="priority" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" 
                                        required>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Baja</option>
                                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Destinatarios -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destinatarios</label>
                            
                            <!-- Destinatarios personalizados -->
                            <div x-show="recipientType === 'custom'">
                                <div class="space-y-2" x-data="{ recipients: [''] }">
                                    <template x-for="(recipient, index) in recipients" :key="index">
                                        <div class="flex items-center space-x-2">
                                            <input type="email" 
                                                   :name="'recipients[' + index + ']'" 
                                                   x-model="recipients[index]"
                                                   placeholder="correo@ejemplo.com" 
                                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-400" 
                                                   required>
                                            <button type="button" 
                                                    @click="recipients.splice(index, 1)" 
                                                    x-show="recipients.length > 1"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" 
                                            @click="recipients.push('')" 
                                            class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        + Agregar destinatario
                                    </button>
                                </div>
                            </div>

                            <!-- Destinatarios de dominios -->
                            <div x-show="recipientType === 'domains'">
                                @if($domains->count() > 0)
                                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                                        @foreach($domains as $domain)
                                            @if($domain->emails)
                                                @php
                                                    $emails = is_string($domain->emails) ? explode(',', $domain->emails) : $domain->emails;
                                                @endphp
                                                @foreach($emails as $email)
                                                    @php $email = trim($email); @endphp
                                                    @if(filter_var($email, FILTER_VALIDATE_EMAIL))
                                                        <label class="flex items-center space-x-2 py-1">
                                                            <input type="checkbox" name="recipients[]" value="{{ $email }}" 
                                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <span class="text-sm">{{ $email }} ({{ $domain->domain_name }})</span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No hay dominios con correos configurados.</p>
                                @endif
                            </div>

                            <!-- Destinatarios de cotizaciones -->
                            <div x-show="recipientType === 'quotations'">
                                @if($quotations->count() > 0)
                                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                                        @foreach($quotations as $quotation)
                                            <label class="flex items-center space-x-2 py-1">
                                                <input type="checkbox" name="recipients[]" value="{{ $quotation->client_email }}" 
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <span class="text-sm">{{ $quotation->client_email }} ({{ $quotation->client_name }})</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No hay cotizaciones con correos configurados.</p>
                                @endif
                            </div>

                            @error('recipients')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contenido -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenido</label>
                            @if($template)
                                <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <p class="text-sm text-blue-800">
                                        <strong>Plantilla #{{ $template->number }}:</strong> {{ $template->title }}
                                    </p>
                                </div>
                            @endif
                            <textarea name="content" id="content" rows="8" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-400" 
                                      required>{{ old('content', $template ? $template->content : '') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Programar envío -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="scheduleMessage" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Programar envío</span>
                            </label>
                            
                            <div x-show="scheduleMessage" class="mt-2">
                                <input type="datetime-local" name="scheduled_at" 
                                       value="{{ old('scheduled_at') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-400">
                                @error('scheduled_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('messages.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function messageForm() {
            return {
                recipientType: '{{ $recipientType }}',
                scheduleMessage: false
            }
        }
    </script>
</x-app-layout>