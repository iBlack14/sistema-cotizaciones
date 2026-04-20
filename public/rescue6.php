<?php
$file = '../resources/views/quotations/index.blade.php';
$contents = file_get_contents($file);

$headerMatch = '<div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">';

$posHeader = strpos($contents, $headerMatch);
if ($posHeader === false) {
    echo "Could not find pagination section.";
    exit;
}

// Find the end of the pagination wrapper div
$endPaginationStr = "</div>\n                    </div>\n                </div>\n            </div>\n        </div>\n";
$posEndPagination = strpos($contents, $endPaginationStr, $posHeader);

if ($posEndPagination === false) {
    // try looser
    $endPaginationStr = "</div>\n                </div>\n            </div>\n        </div>\n";
    $posEndPagination = strpos($contents, $endPaginationStr, $posHeader);
    if ($posEndPagination === false) {
        $endPaginationStr = "</div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n";
        $posEndPagination = strpos($contents, $endPaginationStr, $posHeader);
    }
}

$labelSearch = '<label class="block text-sm font-semibold text-gray-700 mb-2">Buscar Cliente (Nombre,';
$posLabel = strpos($contents, $labelSearch, $posHeader);

if ($posEndPagination !== false && $posLabel !== false) {
    $before = substr($contents, 0, $posEndPagination + strlen($endPaginationStr));
    $after = substr($contents, $posLabel); // the label itself and everything after
    
    $modalHTML = '        
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
    
    file_put_contents($file, $before . $modalHTML . $after);
    echo "Replaced correctly!";
} else {
    echo "Could not find markers.";
}
