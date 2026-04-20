<?php

namespace App\Http\Controllers;

use App\Mail\DomainEmail;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DomainEmailController extends Controller
{
    public function showEmailForm(Domain $domain)
    {
        return view('domains.email', compact('domain'));
    }

    public function sendEmail(Request $request, Domain $domain)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email_type' => 'required|in:corporate,personal,both',
            'send_type' => 'required|in:single,bulk',
        ]);

        $emails = $this->getEmails($domain, $request->email_type);

        if (empty($emails)) {
            return back()->with('error', 'No se encontraron direcciones de correo para enviar.');
        }

        $sentCount = 0;
        $errors = [];

        try {
            if ($request->send_type === 'bulk') {
                // Enviar a todos los correos a la vez
                Mail::to('no-reply@'.$domain->domain_name) // Correo de remitente
                    ->bcc($emails) // Correos en BCC para privacidad
                    ->send(new DomainEmail(
                        $request->subject,
                        $request->message,
                        $domain->domain_name
                    ));
                $sentCount = count($emails);
            } else {
                // Enviar correos individuales
                foreach ($emails as $email) {
                    try {
                        Mail::to($email)
                            ->send(new DomainEmail(
                                $request->subject,
                                $request->message,
                                $domain->domain_name
                            ));
                        $sentCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Error enviando a $email: ".$e->getMessage();
                        Log::error("Error al enviar correo a $email: ".$e->getMessage());
                    }
                }
            }

            $response = [
                'status' => $sentCount > 0 ? 'success' : 'warning',
                'message' => $sentCount > 0
                    ? "Se enviaron {$sentCount} correos exitosamente."
                    : 'No se pudo enviar ningún correo.',
            ];

            if (! empty($errors)) {
                $response['errors'] = $errors;
            }

            return back()->with($response);

        } catch (\Exception $e) {
            Log::error('Error en el envío masivo de correos: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error al enviar los correos: '.$e->getMessage());
        }
    }

    private function getEmails($domain, $type)
    {
        $emails = [];

        if (in_array($type, ['corporate', 'both']) && ! empty($domain->corporate_emails)) {
            $corporateEmails = array_map('trim', explode(',', $domain->corporate_emails));
            $emails = array_merge($emails, $corporateEmails);
        }

        if (in_array($type, ['personal', 'both']) && ! empty($domain->emails)) {
            $personalEmails = array_map('trim', explode(',', $domain->emails));
            $emails = array_merge($emails, $personalEmails);
        }

        // Filtrar correos válidos
        return array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }
}
