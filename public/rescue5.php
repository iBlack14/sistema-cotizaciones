<?php
$file = '../resources/views/quotations/index.blade.php';
$contents = file_get_contents($file);

// Find the first occurrence of "<!-- Export Modal -->"
$first = strpos($contents, '<!-- Export Modal -->');
if ($first !== false) {
    // Find the second occurrence
    $second = strpos($contents, '<!-- Export Modal -->', $first + 10);
    if ($second !== false) {
        // Find the label
        $label = '<label class="block text-sm font-semibold text-gray-700 mb-2">Buscar Cliente (Nombre,';
        $labelPos = strpos($contents, $label, $second);
        
        if ($labelPos !== false) {
            // Cut everything from $second to $labelPos
            $new_contents = substr($contents, 0, $second) . substr($contents, $labelPos);
            file_put_contents($file, $new_contents);
            echo "Removed duplicate modal wrapper!";
        } else {
            echo "Label not found after second modal.";
        }
    } else {
        echo "No second modal found.";
    }
} else {
    echo "No modal found.";
}
