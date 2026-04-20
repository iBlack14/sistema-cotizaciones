<?php

namespace App\Http\Controllers;

use App\Mail\BulkEmail;
use App\Mail\RenewalEmail;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function showEmailForm()
    {
        return view('emails.compose');
    }

    public function sendBulkEmail(Request $request)
    {
        set_time_limit(300); // Aumentar tiempo ejecución a 5 min

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email_type' => 'required|in:corporate,personal,both',
            'send_type' => 'required|in:single,bulk',
            'template_type' => 'nullable|in:simple,renewal',
            'selected_domains' => 'nullable|array',
        ]);

        $domains = Domain::query();

        if ($request->filled('selected_domains')) {
            $domains->whereIn('id', $request->selected_domains);
        }

        $domains->where(function ($q) use ($request) {
            $addedCondition = false;

            if (in_array($request->email_type, ['corporate', 'both'])) {
                $q->whereNotNull('corporate_emails')->where('corporate_emails', '!=', '');
                $addedCondition = true;
            }

            if (in_array($request->email_type, ['personal', 'both'])) {
                if ($addedCondition) {
                    $q->orWhere(function ($subq) {
                        $subq->whereNotNull('emails')->where('emails', '!=', '');
                    });
                } else {
                    $q->whereNotNull('emails')->where('emails', '!=', '');
                }
            }
        });

        $domains = $domains->get();

        $sentCount = 0;
        $errors = [];
        $templateType = $request->template_type ?? 'simple';

        // Pre-cargar el logo para optimizar rendimiento
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? file_get_contents($logoPath) : null;

        foreach ($domains as $domain) {
            $emails = $this->getEmails($domain, $request->email_type);
            if (empty($emails)) {
                continue;
            }

            try {
                $domainData = $this->prepareDomainData($domain, $request->message);

                if ($request->send_type === 'bulk') {
                    // Enviar a todos los correos a la vez
                    if ($templateType === 'renewal') {
                        Mail::to($emails[0])
                            ->bcc(array_slice($emails, 1))
                            ->send(new RenewalEmail(
                                $request->subject,
                                $domainData['clientName'],
                                $domainData['domainName'],
                                $domainData['price'],
                                $domainData['daysUntilExpiration'],
                                $domainData['expirationDate'],
                                $domainData['message'],
                                [],
                                null,
                                $logoData
                            ));
                    } else {
                        Mail::to($emails[0])
                            ->bcc(array_slice($emails, 1))
                            ->send(new BulkEmail($request->subject, $domainData['message']));
                    }
                    $sentCount += count($emails);
                } else {
                    // Enviar correos individuales
                    foreach ($emails as $email) {
                        if ($templateType === 'renewal') {
                            Mail::to($email)
                                ->send(new RenewalEmail(
                                    $request->subject,
                                    $domainData['clientName'],
                                    $domainData['domainName'],
                                    $domainData['price'],
                                    $domainData['daysUntilExpiration'],
                                    $domainData['expirationDate'],
                                    $domainData['message'],
                                    [],
                                    null,
                                    $logoData
                                ));
                        } else {
                            Mail::to($email)
                                ->send(new BulkEmail($request->subject, $domainData['message']));
                        }
                        $sentCount++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Error enviando a dominio {$domain->domain_name}: ".$e->getMessage();
                Log::error('Error al enviar correo: '.$e->getMessage());
            }
        }

        if ($sentCount > 0) {
            return back()->with('success', "✅ Se enviaron {$sentCount} correos exitosamente.");
        } else {
            $errorMsg = 'No se enviaron correos. Verifica que haya direcciones de correo válidas.';
            if (! empty($errors)) {
                $errorMsg .= "\n\nErrores:\n".implode("\n", $errors);
            }

            return back()->with('error', $errorMsg);
        }
    }

    /**
     * Preparar datos del dominio con reemplazo de variables
     */
    private function prepareDomainData($domain, $message)
    {
        // Calcular días hasta expiración
        $daysUntilExpiration = null;
        $expirationDate = null;

        if ($domain->activation_date) {
            try {
                $activationDate = Carbon::parse($domain->activation_date);
                $expirationDateCarbon = $activationDate->copy()->addYear();
                $expirationDate = $expirationDateCarbon->format('d/m/Y');
                $daysUntilExpiration = now()->diffInDays($expirationDateCarbon, false);
                $daysUntilExpiration = (int) $daysUntilExpiration;
            } catch (\Exception $e) {
                Log::warning("Error calculando fecha de expiración para dominio {$domain->id}: ".$e->getMessage());
            }
        }

        // Reemplazar variables en el mensaje
        $processedMessage = $this->replaceVariables($message, [
            '{cliente}' => $domain->client_name ?? 'Cliente',
            '{dominio}' => $domain->domain_name ?? 'N/A',
            '{precio}' => number_format($domain->price ?? 0, 2),
            '{dias}' => $daysUntilExpiration ?? 'N/A',
            '{fecha_vencimiento}' => $expirationDate ?? 'N/A',
        ]);

        return [
            'clientName' => $domain->client_name,
            'domainName' => $domain->domain_name,
            'price' => $domain->price ?? 0,
            'daysUntilExpiration' => $daysUntilExpiration,
            'expirationDate' => $expirationDate,
            'message' => $processedMessage,
        ];
    }

    /**
     * Reemplazar variables en el texto
     */
    private function replaceVariables($text, $variables)
    {
        foreach ($variables as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
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
