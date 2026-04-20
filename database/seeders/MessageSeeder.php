<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No hay usuarios en la base de datos. Crea un usuario primero.');
            return;
        }

        $messages = [
            [
                'subject' => 'Bienvenida a nuestros servicios',
                'content' => "Estimado cliente,\n\nNos complace darle la bienvenida a VIA COMUNICATIVA. Estamos aquí para ayudarle con todos sus proyectos web.\n\nSi tiene alguna pregunta, no dude en contactarnos.\n\nSaludos cordiales,\nEquipo VIA COMUNICATIVA",
                'type' => 'email',
                'priority' => 'normal',
                'status' => 'draft',
                'recipients' => ['cliente@ejemplo.com', 'contacto@empresa.com'],
                'recipient_type' => 'custom',
            ],
            [
                'subject' => 'Recordatorio de renovación de dominio',
                'content' => "Estimado cliente,\n\nLe recordamos que su dominio vence próximamente. Para evitar interrupciones en su servicio, le recomendamos renovar cuanto antes.\n\nDetalles:\n- Dominio: {dominio}\n- Fecha de vencimiento: {fecha_vencimiento}\n- Días restantes: {dias}\n\nPara renovar, contáctenos al WhatsApp o responda este correo.\n\nSaludos,\nVIA COMUNICATIVA",
                'type' => 'email',
                'priority' => 'high',
                'status' => 'sent',
                'recipients' => ['cliente1@ejemplo.com', 'cliente2@ejemplo.com'],
                'recipient_type' => 'domains',
                'sent_at' => now()->subDays(2),
                'metadata' => [
                    'sent_count' => 2,
                    'failed_count' => 0,
                    'created_from' => 'seeder',
                ],
            ],
            [
                'subject' => 'Promoción especial - Desarrollo web',
                'content' => "¡Oferta especial!\n\nEste mes tenemos descuentos especiales en desarrollo web:\n\n- Páginas web informativas: 20% descuento\n- E-commerce: 15% descuento\n- Posicionamiento SEO: 25% descuento\n\n¡No pierdas esta oportunidad!\n\nContáctanos para más información.\n\nVIA COMUNICATIVA",
                'type' => 'email',
                'priority' => 'normal',
                'status' => 'pending',
                'recipients' => ['marketing@cliente1.com', 'info@cliente2.com', 'contacto@cliente3.com'],
                'recipient_type' => 'custom',
                'scheduled_at' => now()->addDays(1),
            ],
            [
                'subject' => 'Seguimiento de cotización',
                'content' => "Estimado {cliente},\n\nEsperamos que se encuentre bien. Le escribimos para hacer seguimiento a la cotización que le enviamos.\n\n¿Ha tenido oportunidad de revisarla? Estamos disponibles para resolver cualquier duda que pueda tener.\n\nQuedamos atentos a sus comentarios.\n\nSaludos cordiales,\nEquipo VIA COMUNICATIVA",
                'type' => 'email',
                'priority' => 'normal',
                'status' => 'draft',
                'recipients' => ['prospecto@empresa.com'],
                'recipient_type' => 'quotations',
            ],
            [
                'subject' => 'Mantenimiento programado',
                'content' => "Estimados clientes,\n\nLes informamos que realizaremos mantenimiento programado en nuestros servidores:\n\nFecha: Domingo 10 de febrero\nHora: 2:00 AM - 4:00 AM\nDuración estimada: 2 horas\n\nDurante este tiempo, algunos servicios podrían verse afectados temporalmente.\n\nGracias por su comprensión.\n\nVIA COMUNICATIVA",
                'type' => 'email',
                'priority' => 'urgent',
                'status' => 'draft',
                'recipients' => ['todos@clientes.com'],
                'recipient_type' => 'custom',
            ],
        ];

        foreach ($messages as $messageData) {
            Message::create(array_merge($messageData, ['user_id' => $user->id]));
        }

        $this->command->info('Se han creado ' . count($messages) . ' mensajes de ejemplo.');
    }
}