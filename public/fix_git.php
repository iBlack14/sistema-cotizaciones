<?php
$output = shell_exec('cd .. && git restore resources/views/quotations/index.blade.php 2>&1');
echo "Output: " . $output;
