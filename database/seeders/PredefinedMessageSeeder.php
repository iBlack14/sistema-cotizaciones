<?php

namespace Database\Seeders;

use App\Models\PredefinedMessage;
use Illuminate\Database\Seeder;

class PredefinedMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PredefinedMessage::count() > 0) {
            return;
        }

        $messages = [
            [
                'title' => 'Bienvenida',
                'content' => '¡Hola! Gracias por contactarnos. ¿En qué podemos ayudarte hoy?',
                'type' => 'whatsapp',
                'number' => 101,
                'is_active' => true,
                'is_favorite' => true,
            ],
            [
                'title' => 'Recordatorio de Pago',
                'content' => 'Estimado cliente, le recordamos que su pago está próximo a vencer. Por favor, realice su pago a la brevedad posible.',
                'type' => 'whatsapp',
                'number' => 102,
                'is_active' => true,
                'is_favorite' => false,
            ],
            [
                'title' => 'Confirmación de Cita',
                'content' => 'Su cita ha sido confirmada para el día de mañana a las 10:00 AM. ¡Lo esperamos!',
                'type' => 'whatsapp',
                'number' => 103,
                'is_active' => true,
                'is_favorite' => true,
            ],
            [
                'title' => 'Promoción Mensual',
                'content' => '¡Aprovecha nuestra promoción del mes! 20% de descuento en todos nuestros servicios. Válido hasta fin de mes.',
                'type' => 'whatsapp',
                'number' => 104,
                'is_active' => true,
                'is_favorite' => false,
            ],
            [
                'title' => 'Encuesta de Satisfacción',
                'content' => '¿Qué tal le pareció nuestro servicio? Por favor, ayúdenos a mejorar respondiendo esta breve encuesta: [ENLACE]',
                'type' => 'whatsapp',
                'number' => 105,
                'is_active' => true,
                'is_favorite' => false,
            ],
        ];

        foreach ($messages as $message) {
            PredefinedMessage::create($message);
        }
    }
}