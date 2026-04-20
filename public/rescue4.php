<?php
$file = '../resources/views/quotations/index.blade.php';
$contents = file_get_contents($file);

$search_target = '<label class="block text-sm font-semibold text-gray-700 mb-2">Buscar Cliente (Nombre,';

$modalWrapperHTML = '
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
                 class="inline-block w-full max-w-xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-fuchsia-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-fuchsia-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-4" id="modal-title">
                                Opciones de Exportación
                            </h3>
                            
                            <form x-bind:action="exportType === \'excel\' ? \'{{ route(\'quotations.export\') }}\' : \'{{ route(\'quotations.export.word\') }}\'" method="GET">
                                <!-- Date Range -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Inicio</label>
                                        <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Fin</label>
                                        <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="mb-4">
';

$new_contents = str_replace($search_target, $modalWrapperHTML . $search_target, $contents);

file_put_contents($file, $new_contents);
echo "Added Export Modal Wrapper!";
