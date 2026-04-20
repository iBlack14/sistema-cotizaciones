@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Enviar Correos Masivos</h1>
        
        @if(session('status'))
            <div class="bg-{{ session('status') === 'success' ? 'green' : 'yellow' }}-100 border border-{{ session('status') === 'success' ? 'green' : 'yellow' }}-400 text-{{ session('status') === 'success' ? 'green' : 'yellow' }}-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('errors') && is_array(session('errors')))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong>Algunos errores ocurrieron:</strong>
                <ul class="mt-2">
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('emails.send') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="email_type" class="block text-gray-700 font-medium">Tipo de correos a enviar:</label>
                <select name="email_type" id="email_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    <option value="corporate">Solo Correos Corporativos</option>
                    <option value="personal">Solo Correos Personales</option>
                    <option value="both" selected>Ambos tipos de correos</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="send_type" class="block text-gray-700 font-medium">Modo de envío:</label>
                <select name="send_type" id="send_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    <option value="bulk">Un solo correo con múltiples destinatarios (BCC)</option>
                    <option value="single">Correos individuales (un correo por destinatario)</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">
                    <strong>BCC</strong> es más rápido pero los destinatarios no verán a los demás.<br>
                    <strong>Individual</strong> es más personalizado pero puede tomar más tiempo.
                </p>
            </div>

            <div class="space-y-2">
                <label for="subject" class="block text-gray-700 font-medium">Asunto del correo:</label>
                <input type="text" name="subject" id="subject" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" 
                       placeholder="Ej: Actualización importante de su servicio" required>
            </div>

            <div class="space-y-2">
                <label for="message" class="block text-gray-700 font-medium">Mensaje:</label>
                <textarea name="message" id="message" rows="8" 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" 
                          placeholder="Escribe tu mensaje aquí..." required></textarea>
                <p class="text-sm text-gray-500">Puedes usar etiquetas HTML básicas para dar formato a tu mensaje.</p>
            </div>

            <div class="flex flex-col sm:flex-row justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                <button type="button" onclick="window.history.back()" 
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <div class="flex space-x-4">
                    <button type="button" id="previewBtn" 
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Vista Previa
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <span id="submitText">Enviar Correos</span>
                        <span id="loadingSpinner" class="hidden">
                            <i class="fas fa-spinner fa-spin"></i> Enviando...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Vista Previa del Correo</h2>
                <button id="closePreview" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="border rounded-lg overflow-hidden">
                <div id="emailPreview" class="p-4 bg-white">
                    <!-- El contenido del correo se cargará aquí -->
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="closePreviewBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = document.getElementById('previewModal');
    const closePreview = document.getElementById('closePreview');
    const closePreviewBtn = document.getElementById('closePreviewBtn');
    const emailPreview = document.getElementById('emailPreview');
    const submitBtn = form.querySelector('button[type="submit"]');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Mostrar/ocultar modal de vista previa
    previewBtn.addEventListener('click', function() {
        const subject = document.getElementById('subject').value || 'Asunto de ejemplo';
        const message = document.getElementById('message').value || 'Contenido de ejemplo';
        
        // Mostrar vista previa del correo
        emailPreview.innerHTML = `
            <div class="mb-4">
                <h3 class="text-lg font-semibold">${subject}</h3>
                <p class="text-sm text-gray-500">Para: [Lista de destinatarios]</p>
            </div>
            <div class="prose max-w-none">
                ${message.replace(/\n/g, '<br>')}
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200 text-sm text-gray-500">
                <p>Este es un correo generado automáticamente. Por favor, no responda a este mensaje.</p>
                <p class="mt-1">© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            </div>
        `;
        
        previewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    // Cerrar modal de vista previa
    function closePreviewModal() {
        previewModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    closePreview.addEventListener('click', closePreviewModal);
    closePreviewBtn.addEventListener('click', closePreviewModal);

    // Cerrar al hacer clic fuera del contenido del modal
    previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
            closePreviewModal();
        }
    });

    // Mostrar spinner de carga al enviar el formulario
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingSpinner.classList.remove('hidden');
    });
});
</script>
@endpush

<style>
.prose {
    line-height: 1.6;
    color: #374151;
}
.prose h1, .prose h2, .prose h3, .prose h4 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 600;
    line-height: 1.25;
}
.prose p {
    margin-top: 1em;
    margin-bottom: 1em;
}
.prose a {
    color: #7c3aed;
    text-decoration: underline;
}
.prose ul, .prose ol {
    padding-left: 1.5em;
    margin-top: 1em;
    margin-bottom: 1em;
}
.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
</style>
@endsection
