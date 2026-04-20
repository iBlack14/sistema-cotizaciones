<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\FlyerTemplate;
use App\Models\Message;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Mostrar lista de mensajes
     */
    public function index(Request $request)
    {
        // Obtener mensajes predeterminados
        $predefinedMessages = \App\Models\PredefinedMessage::active()->ordered()->get();
        $flyers = FlyerTemplate::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();
        foreach ($flyers as $flyer) {
            $this->ensurePublicStorageMirror($flyer->path ?: ('flyers/'.$flyer->filename));
        }

        // Obtener categorías únicas para los tabs
        $messageCategories = \App\Models\PredefinedMessage::active()
            ->distinct('type')
            ->pluck('type')
            ->filter()
            ->values();

        // Contar mensajes ocultos (con manejo de error por si se elimina la columna)
        $hiddenCount = 0;
        try {
            $hiddenCount = Message::where('user_id', Auth::id())->hidden()->count();
        } catch (\Exception $e) {
            \Log::warning('Columna is_hidden no encontrada');
        }

        return view('messages.index', compact('predefinedMessages', 'messageCategories', 'hiddenCount', 'flyers'));
    }

    /**
     * Mostrar mensajes ocultos
     */
    public function hidden(Request $request)
    {
        try {
            $messages = Message::where('user_id', Auth::id())
                ->hidden()
                ->latest('hidden_at')
                ->paginate(15);
        } catch (\Exception $e) {
            return redirect()->route('messages.index')->with('error', 'La funcionalidad de mensajes ocultos no está disponible.');
        }

        return view('messages.hidden', compact('messages'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        $recipientType = $request->get('recipient_type', 'custom');
        $templateId = $request->get('template');
        $type = $request->get('type', 'email');

        // Obtener plantilla si se especifica
        $template = null;
        if ($templateId) {
            $template = \App\Models\PredefinedMessage::find($templateId);
        }

        // Obtener datos según el tipo de destinatario
        $domains = collect();
        $quotations = collect();

        if ($recipientType === 'domains') {
            $domains = Domain::where('user_id', Auth::id())
                ->where('status', 'activo')
                ->orderBy('domain_name')
                ->get();
        } elseif ($recipientType === 'quotations') {
            $quotations = Quotation::where('user_id', Auth::id())
                ->whereNotNull('client_email')
                ->latest()
                ->get();
        }

        return view('messages.create', compact('recipientType', 'domains', 'quotations', 'template', 'type'));
    }

    /**
     * Almacenar nuevo mensaje
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:email,whatsapp,sms,notification',
            'priority' => 'required|in:low,normal,high,urgent',
            'recipient_type' => 'required|in:domains,quotations,custom',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $status = $request->filled('scheduled_at') ? 'pending' : 'draft';

        $message = Message::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'content' => $request->input('content'),
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $status,
            'recipients' => $request->recipients,
            'recipient_type' => $request->recipient_type,
            'scheduled_at' => $request->scheduled_at,
            'metadata' => [
                'created_from' => 'web',
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Mensaje creado exitosamente.');
    }

    /**
     * Mostrar mensaje específico
     */
    public function show(Message $message)
    {
        $this->authorize('view', $message);

        return view('messages.show', compact('message'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Message $message)
    {
        $this->authorize('update', $message);

        // Solo se pueden editar borradores
        if ($message->status !== 'draft') {
            return redirect()->route('messages.show', $message)
                ->with('error', 'Solo se pueden editar mensajes en borrador.');
        }

        $recipientType = $message->recipient_type;

        $domains = collect();
        $quotations = collect();

        if ($recipientType === 'domains') {
            $domains = Domain::where('user_id', Auth::id())
                ->where('status', 'activo')
                ->orderBy('domain_name')
                ->get();
        } elseif ($recipientType === 'quotations') {
            $quotations = Quotation::where('user_id', Auth::id())
                ->whereNotNull('client_email')
                ->latest()
                ->get();
        }

        return view('messages.edit', compact('message', 'recipientType', 'domains', 'quotations'));
    }

    /**
     * Actualizar mensaje
     */
    public function update(Request $request, Message $message)
    {
        $this->authorize('update', $message);

        // Solo se pueden editar borradores
        if ($message->status !== 'draft') {
            return redirect()->route('messages.show', $message)
                ->with('error', 'Solo se pueden editar mensajes en borrador.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:email,whatsapp,sms,notification',
            'priority' => 'required|in:low,normal,high,urgent',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $status = $request->filled('scheduled_at') ? 'pending' : 'draft';

        $message->update([
            'subject' => $request->subject,
            'content' => $request->input('content'),
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $status,
            'recipients' => $request->recipients,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Mensaje actualizado exitosamente.');
    }

    /**
     * Enviar mensaje
     */
    public function send(Message $message)
    {
        $this->authorize('update', $message);

        if (! in_array($message->status, ['draft', 'pending'])) {
            return redirect()->route('messages.show', $message)
                ->with('error', 'Este mensaje ya fue enviado o no se puede enviar.');
        }

        try {
            $successCount = 0;
            $failedEmails = [];

            foreach ($message->recipients as $email) {
                try {
                    Mail::raw($message->content, function ($mail) use ($message, $email) {
                        $mail->to($email)
                            ->subject($message->subject)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                    $successCount++;
                } catch (\Exception $e) {
                    $failedEmails[] = $email;
                    Log::error("Error enviando mensaje a {$email}: ".$e->getMessage());
                }
            }

            // Actualizar estado del mensaje
            $status = empty($failedEmails) ? 'sent' : ($successCount > 0 ? 'sent' : 'failed');

            $message->update([
                'status' => $status,
                'sent_at' => now(),
                'metadata' => array_merge($message->metadata ?? [], [
                    'sent_count' => $successCount,
                    'failed_count' => count($failedEmails),
                    'failed_emails' => $failedEmails,
                ]),
            ]);

            if ($successCount > 0) {
                $successMessage = "Mensaje enviado exitosamente a {$successCount} destinatario(s).";
                if (! empty($failedEmails)) {
                    $successMessage .= ' Falló el envío a '.count($failedEmails).' destinatario(s).';
                }

                return redirect()->route('messages.show', $message)
                    ->with('success', $successMessage);
            } else {
                return redirect()->route('messages.show', $message)
                    ->with('error', 'No se pudo enviar el mensaje a ningún destinatario.');
            }

        } catch (\Exception $e) {
            Log::error('Error enviando mensaje: '.$e->getMessage());

            $message->update([
                'status' => 'failed',
                'metadata' => array_merge($message->metadata ?? [], [
                    'error' => $e->getMessage(),
                ]),
            ]);

            return redirect()->route('messages.show', $message)
                ->with('error', 'Error al enviar el mensaje: '.$e->getMessage());
        }
    }

    /**
     * Duplicar mensaje
     */
    public function duplicate(Message $message)
    {
        $this->authorize('view', $message);

        $newMessage = $message->replicate();
        $newMessage->status = 'draft';
        $newMessage->sent_at = null;
        $newMessage->scheduled_at = null;
        $newMessage->subject = 'Copia de: '.$message->subject;
        $newMessage->save();

        return redirect()->route('messages.edit', $newMessage)
            ->with('success', 'Mensaje duplicado exitosamente.');
    }

    /**
     * Eliminar mensaje
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Mensaje eliminado exitosamente.');
    }

    /**
     * API para obtener destinatarios según el tipo
     */
    public function getRecipients(Request $request)
    {
        $type = $request->get('type');
        $recipients = [];

        switch ($type) {
            case 'domains':
                $domains = Domain::where('user_id', Auth::id())
                    ->where('status', 'activo')
                    ->whereNotNull('emails')
                    ->get();

                foreach ($domains as $domain) {
                    $emails = is_string($domain->emails) ? explode(',', $domain->emails) : $domain->emails;
                    if (is_array($emails)) {
                        foreach ($emails as $email) {
                            $email = trim($email);
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $recipients[] = [
                                    'email' => $email,
                                    'name' => $domain->domain_name,
                                    'type' => 'domain',
                                ];
                            }
                        }
                    }
                }
                break;

            case 'quotations':
                $quotations = Quotation::where('user_id', Auth::id())
                    ->whereNotNull('client_email')
                    ->get();

                foreach ($quotations as $quotation) {
                    if (filter_var($quotation->client_email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'email' => $quotation->client_email,
                            'name' => $quotation->client_name,
                            'type' => 'quotation',
                        ];
                    }
                }
                break;
        }

        return response()->json($recipients);
    }

    /**
     * Ocultar mensaje (soft hide)
     */
    public function hide(Message $message)
    {
        $this->authorize('update', $message);

        $message->hide();

        return response()->json([
            'success' => true,
            'message' => 'Mensaje ocultado exitosamente.',
        ]);
    }

    /**
     * Mostrar mensaje oculto
     */
    public function unhide(Message $message)
    {
        $this->authorize('update', $message);

        $message->unhide();

        return response()->json([
            'success' => true,
            'message' => 'Mensaje restaurado exitosamente.',
        ]);
    }

    /**
     * Obtener mensajes recientes para widget
     */
    public function getRecentMessages(Request $request)
    {
        $limit = $request->get('limit', 10);

        $messages = Message::where('user_id', Auth::id())
            ->recent()
            ->limit($limit)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message_number' => $message->message_number,
                    'subject' => $message->subject,
                    'content' => Str::limit(strip_tags($message->content), 100),
                    'type' => $message->type,
                    'status' => $message->status,
                    'status_badge' => $message->status_badge,
                    'priority_badge' => $message->priority_badge,
                    'recipients_count' => $message->recipients_count,
                    'created_at' => $message->created_at->format('d/m/Y H:i'),
                    'created_at_human' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json($messages);
    }

    /**
     * Exportar mensajes a PDF
     */
    public function exportPDF(Request $request)
    {
        $query = Message::where('user_id', Auth::id())
            ->orderBy('message_number');

        // Filtros opcionales
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        if ($request->filled('include_hidden')) {
            // Incluir mensajes ocultos si se especifica
        } else {
            $query->visible();
        }

        $messages = $query->get();

        $pdf = \PDF::loadView('messages.pdf', compact('messages'));

        return $pdf->download('mensajes-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Registrar envío de WhatsApp (para estadísticas)
     */
    public function logWhatsAppSend(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:15',
            'message_id' => 'nullable|exists:predefined_messages,id',
            'content' => 'nullable|string|max:5000',
        ]);

        // Permite registrar envios desde Soporte con plantilla o mensaje libre.
        $predefinedMessage = null;
        if ($request->filled('message_id')) {
            $predefinedMessage = \App\Models\PredefinedMessage::find($request->message_id);
        }

        $subject = $predefinedMessage
            ? 'WhatsApp: '.$predefinedMessage->title
            : 'WhatsApp: mensaje directo';

        $content = $predefinedMessage?->content
            ?? trim((string) $request->input('content', ''))
            ?: 'Mensaje enviado desde soporte';

        Message::create([
            'user_id' => Auth::id(),
            'subject' => $subject,
            'content' => $content,
            'type' => 'whatsapp',
            'priority' => 'normal',
            'status' => 'sent',
            'recipients' => ['+51'.$request->number],
            'recipient_type' => 'custom',
            'sent_at' => now(),
            'metadata' => [
                'whatsapp_number' => $request->number,
                'predefined_message_id' => $request->message_id,
                'sent_from' => 'whatsapp_widget',
                'content_preview' => Str::limit($content, 120),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Envío registrado exitosamente.',
        ]);
    }

    /**
     * Toggle favorite status for predefined message
     */
    public function toggleFavorite(Request $request, $predefined)
    {
        $message = \App\Models\PredefinedMessage::findOrFail($predefined);

        // Toggle favorite status
        $message->is_favorite = ! $message->is_favorite;
        $message->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $message->is_favorite,
            'message' => $message->is_favorite ? 'Mensaje marcado como favorito' : 'Mensaje eliminado de favoritos',
        ]);
    }

    public function storeFlyer(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string|max:5000',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('image');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('flyers', $filename, 'public');
        $this->ensurePublicStorageMirror($path);

        FlyerTemplate::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'caption' => $request->input('caption'),
            'filename' => $filename,
            'path' => $path,
            'is_active' => true,
            'order' => (FlyerTemplate::where('user_id', Auth::id())->max('order') ?? 0) + 1,
        ]);

        return redirect()->route('messages.index')->with('success', 'Flayer guardado correctamente.');
    }

    public function deleteFlyer(FlyerTemplate $flyer)
    {
        if ($flyer->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($flyer->path);
        $publicPath = public_path('storage/'.ltrim($flyer->path ?: ('flyers/'.$flyer->filename), '/'));
        if (is_file($publicPath)) {
            @unlink($publicPath);
        }
        $flyer->delete();

        return redirect()->route('messages.index')->with('success', 'Flayer eliminado correctamente.');
    }

    private function ensurePublicStorageMirror(string $relativePath): void
    {
        $relativePath = ltrim($relativePath, '/');
        $source = storage_path('app/public/'.$relativePath);
        $target = public_path('storage/'.$relativePath);

        if (! is_file($source) || is_file($target)) {
            return;
        }

        $targetDir = dirname($target);
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        @copy($source, $target);
    }
}
