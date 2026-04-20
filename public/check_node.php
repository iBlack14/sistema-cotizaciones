<?php
// BORRAR DESPUÉS DE USAR
// Prueba si el servicio Node.js responde internamente
// Sube a public/ y abre en el navegador

echo "<h2>🔍 Test interno del servicio WhatsApp</h2>";

// Intentar conectar al servicio Node.js por localhost con diferentes puertos
$ports = [3000, 3001, 3002, 8080, 8081, 5000];
$found = false;

foreach ($ports as $port) {
    $url = "http://127.0.0.1:{$port}/api/status";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo "<p style='color:green'>✅ Puerto <strong>{$port}</strong>: RESPONDE → <code>{$result}</code></p>";
        $found = true;
    } else {
        echo "<p style='color:red'>❌ Puerto <strong>{$port}</strong>: Sin respuesta (HTTP {$httpCode})</p>";
    }
}

if (!$found) {
    echo "<h3 style='color:orange'>⚠️ El servicio Node.js no responde internamente.</h3>";
    echo "<p>Posibles causas: App no iniciada, o Passenger usa socket Unix en lugar de TCP.</p>";
}

echo "<hr><small style='color:red'>⚠️ BORRA este archivo después de usarlo.</small>";
