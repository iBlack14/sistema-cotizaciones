<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\PredefinedMessage;
use App\Models\Quotation;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class SoporteController extends Controller
{
    public function index()
    {
        $quotations = Quotation::orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $domains = Domain::with('user')
            ->orderBy('domain_name', 'asc')
            ->get();

        $soporteMessages = PredefinedMessage::where('type', 'whatsapp')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soporte.index', compact('quotations', 'domains', 'soporteMessages'));
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $lastNumber = PredefinedMessage::max('number') ?? 0;

        $message = PredefinedMessage::create([
            'number' => $lastNumber + 1,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => 'whatsapp',
            'is_active' => true,
            'is_favorite' => false,
            'order' => PredefinedMessage::where('type', 'whatsapp')->count() + 1,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function deleteMessage($id)
    {
        $message = PredefinedMessage::findOrFail($id);
        $message->delete();

        return response()->json(['success' => true]);
    }

    public function qr(Request $request, WhatsAppService $wa)
    {
        return response()->json($wa->getQr($request->query('instance')));
    }

    public function status(Request $request, WhatsAppService $wa)
    {
        return response()->json($wa->getStatus($request->query('instance')));
    }

    public function chats(Request $request, WhatsAppService $wa)
    {
        return response()->json($wa->getChats($request->query('instance')));
    }

    public function send(Request $request, WhatsAppService $wa)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:32',
            'message' => 'required|string|max:5000',
        ]);

        return response()->json($wa->sendMessage(
            (string) $request->query('instance', ''),
            (string) $validated['phone'],
            (string) $validated['message']
        ));
    }

    public function disconnect(Request $request, WhatsAppService $wa)
    {
        return response()->json($wa->disconnect($request->query('instance')));
    }
}
