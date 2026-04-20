<?php
$file = '../resources/views/quotations/index.blade.php';
$contents = file_get_contents($file);

$search = "                                            .catch(error => {\n                                                console.error('Error:', error);\n                                                this.loading = false;\n                                                alert('Error al enviar el correo. Verifica que el cliente tenga un email válido.');\n                                            });\n                                        },";

$correctOpenWhatsApp = "
                                        openWhatsApp(mode = null) {
                                            let phone = '{{ \$quotation->client_phone }}'.replace(/\D/g, '');
                                            if (!phone.startsWith('51') && phone.length === 9) {
                                                phone = '51' + phone;
                                            }

                                            let template = '';
                                            const customMsgObj = @json(isset(\$customMessages) ? \$customMessages->pluck('content', 'title') : collect());
                                            
                                            if (mode === 'resend') {
                                                template = `{{ \$settings['resend_whatsapp_message'] ?? '' }}`;
                                            } else if (this.message === 'Confirmación') {
                                                template = `{{ \$settings['confirmation_whatsapp_message'] ?? '' }}`;
                                            } else if (this.message === 'Servicio') {
                                                template = `{{ \$settings['service_whatsapp_message'] ?? '' }}`;
                                            } else if (this.message === 'Acceso de su servicio') {
                                                template = `{{ \$settings['access_whatsapp_message'] ?? '' }}`;
                                            } else if (customMsgObj[this.message]) {
                                                template = customMsgObj[this.message] || '';
                                            } else {
                                                template = `{{ \$settings['quotation_whatsapp_message'] ?? '' }}`;
                                            }

                                            // Fallbacks if settings are empty
                                            if (!template) {
                                                if (mode === 'resend') template = 'Hola [Nombre], te reenvío la cotización. Saludos.';
                                                else if (this.message === 'Confirmación') template = 'Hola [Nombre], ¿pudiste revisar la cotización?';
                                                else if (this.message === 'Servicio') template = 'Hola [Nombre], coordinemos el servicio.';
                                                else if (this.message === 'Acceso de su servicio') template = 'Hola [Nombre], necesitamos accesos.';
                                                else template = 'Hola [Nombre], adjunto cotización.';
                                            }

                                            // Replacements
                                            let messageText = template
                                                .replace(/\[Nombre\]/g, '{{ \$quotation->client_name ?? \$quotation->client_company }}')
                                                .replace(/\[Empresa\]/g, '{{ \$quotation->client_company ?? \"\" }}')
                                                .replace(/\[RUC\]/g, '{{ \$quotation->client_ruc ?? \"\" }}')
                                                .replace(/\[Fecha\]/g, '{{ \Carbon\Carbon::parse(\$quotation->date)->format(\"d/m/Y\") }}')
                                                .replace(/\[Servicio\]/g, '{{ \$quotation->items->first()->service_name ?? \"\" }}')
                                                .replace(/\[Total\]/g, '{{ number_format(\$quotation->total, 2) }}')
                                                .replace(/\[Link\]/g, '{{ \Illuminate\Support\Facades\URL::signedRoute(\"quotations.download\", \$quotation) }}');

                                            let url = `https://wa.me/\${phone}?text=\${encodeURIComponent(messageText)}`;
                                            window.open(url, '_blank');
                                        }
                                    }\">";

// Use preg_replace
$pattern = '/(' . preg_quote($search, '/') . ').*?(<td class="px-3 py-4 text-sm font-bold text-center">)/s';
$new_contents = preg_replace($pattern, "$1" . $correctOpenWhatsApp . "\n                                        $2", $contents);

if ($new_contents === null || $new_contents === $contents) {
    echo "No replacement occurred. Check regex.";
} else {
    file_put_contents($file, $new_contents);
    echo "Fixed table rendering successfully!";
}
