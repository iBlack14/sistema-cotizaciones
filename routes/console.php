<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Domain;
use App\Mail\RenewalEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('domains:send-alerts', function () {
    $this->info('Consultando dominios que vencen en 30 días...');
    
    // Obtenemos dominios que vencen exactamente en 30 días
    $targetDate = now()->addDays(30)->toDateString();
    $domains = Domain::whereDate('expiration_date', $targetDate)->get();

    foreach ($domains as $domain) {
        $email = $domain->emails ?? $domain->corporate_emails;

        if (!$email) {
            $this->warn("El dominio {$domain->domain_name} no tiene correo asignado. Saltando...");
            continue;
        }

        try {
            $subject = "Aviso de Renovación: {$domain->domain_name}";
            $days = 30;
            
            Mail::to($email)->send(new RenewalEmail(
                $subject,
                $domain->client_name,
                $domain->domain_name,
                $domain->price,
                $days,
                $domain->expiration_date->format('d/m/Y'),
                "Tu dominio {$domain->domain_name} está próximo a vencer. Por favor, procede con la renovación para evitar interrupciones."
            ));

            $this->info("Mensaje enviado exitosamente a {$email} para el dominio {$domain->domain_name}");
        } catch (\Exception $e) {
            $this->error("Error enviando a {$email}: " . $e->getMessage());
        }
    }
})->purpose('Enviar correos de renovación a los dominios que vencen en 30 días');

Schedule::command('domains:send-alerts')->dailyAt('09:00');
