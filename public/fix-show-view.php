<?php
$file = 'resources/views/quotations/show.blade.php';
$contents = file_get_contents($file);

$search = ".replace(/\[Link\]/g, '{{ \Illuminate\Support\Facades\URL::signedRoute(\"quotations.download\", \$quotation) }}');";
$replace = ".replace(/\[Link\]/g, '{{ \$quotation->slug ? route(\"quotations.public\", \$quotation->slug) : \Illuminate\Support\Facades\URL::signedRoute(\"quotations.download\", \$quotation) }}');";

if (strpos($contents, $search) !== false) {
    $contents = str_replace($search, $replace, $contents);
    file_put_contents($file, $contents);
    echo "SUCCESS: Link generation in show.blade.php updated.\n";
} else {
    echo "FAILURE: Could not find search string in show.blade.php.\n";
    // Try without spaces
    $search_loose = ".replace(/\[Link\]/g, '{{ \\\\Illuminate";
    if (strpos($contents, $search_loose) !== false) {
        echo "Found loose match.\n";
    }
}
