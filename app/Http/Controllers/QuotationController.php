<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuotationController extends Controller
{
    private function getAvailableServices(): array
    {
        $defaultServices = [
            'WEB INFORMATIVA',
            'WEB E-COMMERCE',
            'WEB AULA VIRTUAL',
            'POSICIONAMIENTO SEO',
            'LICENCIA DE ANTIVIRUS',
            'PLUGIN YOAST SEO',
            'RESTRUCTURACION BASICA',
            'RESTRUCTURACION E-COMMERCE',
            'WEB FUSION E-COMMERCE',
            'WEB FUSION AULA VIRTUAL',
            'REDES SOCIALES',
        ];

        $mappedServices = \App\Models\ServiceMapping::query()
            ->select('service_name')
            ->distinct()
            ->pluck('service_name')
            ->filter(fn ($name) => ! empty(trim((string) $name)))
            ->map(fn ($name) => trim((string) $name))
            ->values()
            ->all();

        return collect($defaultServices)
            ->merge($mappedServices)
            ->unique()
            ->values()
            ->all();
    }

    public function create()
    {
        $images = \App\Models\ServiceImage::where('is_active', true)->orderBy('order')->get();
        $services = $this->getAvailableServices();

        return view('quotations.create', compact('images', 'services'));
    }

    private function parseLlamarParts(string $value): array
    {
        $raw = trim($value);
        $match = preg_match('/(\d{1,2})\s*(?:de\s+)?(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)/i', $raw, $matches);

        if (! $match) {
            return ['day' => '', 'month' => ''];
        }

        $dayNumber = (int) $matches[1];
        $day = ($dayNumber >= 1 && $dayNumber <= 31) ? (string) $dayNumber : '';
        $month = strtolower($matches[2]);
        $month = str_replace('setiembre', 'septiembre', $month);

        return ['day' => $day, 'month' => $month];
    }

    public function notes()
    {
        $services = $this->getAvailableServices();
        $headerDate = now()->locale('es')->translatedFormat('d F');
        $savedNotes = \App\Models\Note::query()
            ->where('user_id', Auth::id())
            ->orderBy('id')
            ->get(['id', 'cliente', 'ruc', 'telefono', 'llamar', 'servicio', 'descripcion', 'collapsed', 'pin_side'])
            ->map(function ($note) {
                $llamarRaw = (string) ($note->llamar ?? '');
                $parsed = $this->parseLlamarParts($llamarRaw);

                return [
                    'id' => (int) $note->id,
                    'cliente' => (string) ($note->cliente ?? ''),
                    'ruc' => (string) ($note->ruc ?? ''),
                    'telefono' => (string) ($note->telefono ?? ''),
                    'llamar' => $llamarRaw,
                    'llamarDay' => $parsed['day'],
                    'llamarMonth' => $parsed['month'],
                    'servicio' => (string) ($note->servicio ?? ''),
                    'descripcion' => (string) ($note->descripcion ?? ''),
                    'collapsed' => (bool) ($note->collapsed ?? false),
                    'pin_side' => in_array($note->pin_side, ['left', 'right'], true) ? $note->pin_side : 'right',
                ];
            })
            ->values()
            ->all();

        return view('quotations.notes', compact('services', 'savedNotes', 'headerDate'));
    }

    public function saveNotes(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('RAW REQUEST CONTENT: '.$request->getContent());
        \Illuminate\Support\Facades\Log::info('saveNotes requested', $request->all());
        $validated = $request->validate([
            'notes' => 'nullable|array',
            'notes.*.id' => 'nullable|integer',
            'notes.*.local_key' => 'nullable|string|max:100',
            'notes.*.cliente' => 'nullable|string|max:255',
            'notes.*.ruc' => 'nullable|string|max:20',
            'notes.*.telefono' => 'nullable|string|max:50',
            'notes.*.llamar' => 'nullable|string|max:255',
            'notes.*.llamar_day' => 'nullable|string|max:2',
            'notes.*.llamar_month' => 'nullable|string|max:20',
            'notes.*.servicio' => 'nullable|string|max:255',
            'notes.*.descripcion' => 'nullable|string|max:2000',
            'notes.*.collapsed' => 'nullable|boolean',
            'notes.*.pin_side' => 'nullable|in:left,right',
        ]);

        $notes = collect($validated['notes'] ?? [])
            ->map(function (array $note) {
                $llamarDay = trim((string) ($note['llamar_day'] ?? ''));
                $llamarMonth = mb_strtolower(trim((string) ($note['llamar_month'] ?? '')));
                $llamarMonth = str_replace('setiembre', 'septiembre', $llamarMonth);
                $llamarText = trim((string) ($note['llamar'] ?? ''));
                if ($llamarDay !== '' && $llamarMonth !== '') {
                    $llamarText = $llamarDay.' '.$llamarMonth;
                }

                return [
                    'id' => isset($note['id']) && $note['id'] !== '' ? (int) $note['id'] : null,
                    'local_key' => trim((string) ($note['local_key'] ?? '')),
                    'cliente' => trim((string) ($note['cliente'] ?? '')),
                    'ruc' => trim((string) ($note['ruc'] ?? '')),
                    'telefono' => trim((string) ($note['telefono'] ?? '')),
                    'llamar' => $llamarText,
                    'servicio' => trim((string) ($note['servicio'] ?? '')),
                    'descripcion' => trim((string) ($note['descripcion'] ?? '')),
                    'collapsed' => filter_var($note['collapsed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'pin_side' => in_array(($note['pin_side'] ?? 'right'), ['left', 'right'], true) ? $note['pin_side'] : 'right',
                ];
            })
            ->filter(fn (array $note) => collect($note)->except(['id', 'local_key', 'collapsed', 'pin_side'])->some(fn ($value) => $value !== ''))
            ->values();

        $userId = Auth::id();
        $existingIds = \App\Models\Note::query()
            ->where('user_id', $userId)
            ->pluck('id')
            ->all();

        $keptIds = [];
        $mappings = [];

        if ($notes->isNotEmpty()) {
            foreach ($notes as $noteData) {
                $note = null;
                if (! empty($noteData['id'])) {
                    $note = \App\Models\Note::query()
                        ->where('user_id', $userId)
                        ->where('id', $noteData['id'])
                        ->first();
                }

                if (! $note) {
                    $note = new \App\Models\Note;
                    $note->user_id = $userId;
                }

                $note->cliente = $noteData['cliente'];
                $note->ruc = $noteData['ruc'];
                $note->telefono = $noteData['telefono'];
                if ($noteData['llamar'] !== '' || ! $note->exists || empty($note->llamar)) {
                    $note->llamar = $noteData['llamar'];
                }
                $note->servicio = $noteData['servicio'];
                $note->descripcion = $noteData['descripcion'];
                $note->collapsed = $noteData['collapsed'];
                $note->pin_side = $noteData['pin_side'];
                $note->save();

                $keptIds[] = $note->id;
                if (! empty($noteData['local_key'])) {
                    $mappings[] = [
                        'local_key' => $noteData['local_key'],
                        'id' => $note->id,
                    ];
                }
            }
        }

        $idsToDelete = array_diff($existingIds, $keptIds);
        if (! empty($idsToDelete)) {
            \App\Models\Note::query()
                ->where('user_id', $userId)
                ->whereIn('id', $idsToDelete)
                ->delete();
        }

        $savedNotes = \App\Models\Note::query()
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get(['id', 'cliente', 'ruc', 'telefono', 'llamar', 'servicio', 'descripcion', 'collapsed', 'pin_side'])
            ->map(function ($note) {
                $llamarRaw = (string) ($note->llamar ?? '');
                $parsed = $this->parseLlamarParts($llamarRaw);

                return [
                    'id' => (int) $note->id,
                    'cliente' => (string) ($note->cliente ?? ''),
                    'ruc' => (string) ($note->ruc ?? ''),
                    'telefono' => (string) ($note->telefono ?? ''),
                    'llamar' => $llamarRaw,
                    'llamarDay' => $parsed['day'],
                    'llamarMonth' => $parsed['month'],
                    'servicio' => (string) ($note->servicio ?? ''),
                    'descripcion' => (string) ($note->descripcion ?? ''),
                    'collapsed' => (bool) ($note->collapsed ?? false),
                    'pin_side' => in_array($note->pin_side, ['left', 'right'], true) ? $note->pin_side : 'right',
                ];
            })
            ->values()
            ->all();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'mappings' => $mappings,
            ]);
        }

        return back()->with('status', 'Notas guardadas correctamente.');
    }

    public function exportNotesWord(Request $request)
    {
        $monthOrder = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
        ];
        $extractMonth = static function (string $value): ?string {
            if (preg_match('/\b(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)\b/u', mb_strtolower(trim($value)), $matches)) {
                return $matches[1] === 'setiembre' ? 'septiembre' : $matches[1];
            }

            return null;
        };

        $validated = $request->validate([
            'notes' => 'required|array|min:1',
            'notes.*.cliente' => 'nullable|string|max:255',
            'notes.*.ruc' => 'nullable|string|max:20',
            'notes.*.telefono' => 'nullable|string|max:50',
            'notes.*.llamar' => 'nullable|string|max:255',
            'notes.*.servicio' => 'nullable|string|max:255',
            'notes.*.descripcion' => 'nullable|string|max:2000',
            'export_month' => 'nullable|in:enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,setiembre,octubre,noviembre,diciembre',
        ]);

        $notes = collect($validated['notes'] ?? [])
            ->map(function (array $note) {
                return [
                    'cliente' => trim((string) ($note['cliente'] ?? '')),
                    'ruc' => trim((string) ($note['ruc'] ?? '')),
                    'telefono' => trim((string) ($note['telefono'] ?? '')),
                    'llamar' => trim((string) ($note['llamar'] ?? '')),
                    'servicio' => trim((string) ($note['servicio'] ?? '')),
                    'descripcion' => trim((string) ($note['descripcion'] ?? '')),
                ];
            })
            ->filter(fn (array $note) => collect($note)->some(fn ($value) => $value !== ''))
            ->values()
            ->all();

        $selectedMonth = mb_strtolower(trim((string) ($validated['export_month'] ?? '')));
        if ($selectedMonth === 'setiembre') {
            $selectedMonth = 'septiembre';
        }

        if ($selectedMonth !== '') {
            $notes = collect($notes)
                ->filter(fn (array $note) => $extractMonth((string) ($note['llamar'] ?? '')) === $selectedMonth)
                ->values()
                ->all();
        }

        if (empty($notes)) {
            return back()->withErrors([
                'notes' => $selectedMonth !== ''
                    ? 'No hay notas para el mes seleccionado.'
                    : 'Agrega al menos una nota con contenido para exportar.',
            ])->withInput();
        }

        $monthIndex = array_flip($monthOrder);
        $foundMonths = collect($notes)
            ->pluck('llamar')
            ->map(fn ($value) => $extractMonth((string) $value))
            ->filter()
            ->unique()
            ->sortBy(fn ($month) => $monthIndex[$month] ?? 99)
            ->values()
            ->all();

        if (empty($foundMonths)) {
            $foundMonths = [$selectedMonth !== '' ? $selectedMonth : now()->locale('es')->translatedFormat('F')];
        }

        $exportMonthTitle = (count($foundMonths) > 1 ? 'Meses: ' : 'Mes: ')
            .implode(', ', array_map('ucfirst', $foundMonths));
        $fileName = 'notas_'.\Illuminate\Support\Str::slug(implode('-', $foundMonths)).'.doc';

        return response()->view('quotations.notes-word', [
            'notes' => $notes,
            'exportMonthTitle' => $exportMonthTitle,
        ])
            ->header('Content-Type', 'application/vnd.ms-word')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }

    public function exportNotesPdf(Request $request)
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('quotations.notes');
        }

        $monthOrder = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
        ];
        $extractMonth = static function (string $value): ?string {
            if (preg_match('/\b(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)\b/u', mb_strtolower(trim($value)), $matches)) {
                return $matches[1] === 'setiembre' ? 'septiembre' : $matches[1];
            }

            return null;
        };

        $validated = $request->validate([
            'notes' => 'required|array|min:1',
            'notes.*.cliente' => 'nullable|string|max:255',
            'notes.*.ruc' => 'nullable|string|max:20',
            'notes.*.telefono' => 'nullable|string|max:50',
            'notes.*.llamar' => 'nullable|string|max:255',
            'notes.*.servicio' => 'nullable|string|max:255',
            'notes.*.descripcion' => 'nullable|string|max:2000',
            'export_month' => 'nullable|in:enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,setiembre,octubre,noviembre,diciembre',
        ]);

        $notes = collect($validated['notes'] ?? [])
            ->map(function (array $note) {
                return [
                    'cliente' => trim((string) ($note['cliente'] ?? '')),
                    'ruc' => trim((string) ($note['ruc'] ?? '')),
                    'telefono' => trim((string) ($note['telefono'] ?? '')),
                    'llamar' => trim((string) ($note['llamar'] ?? '')),
                    'servicio' => trim((string) ($note['servicio'] ?? '')),
                    'descripcion' => trim((string) ($note['descripcion'] ?? '')),
                ];
            })
            ->filter(fn (array $note) => collect($note)->some(fn ($value) => $value !== ''))
            ->values()
            ->all();

        $selectedMonth = mb_strtolower(trim((string) ($validated['export_month'] ?? '')));
        if ($selectedMonth === 'setiembre') {
            $selectedMonth = 'septiembre';
        }

        if ($selectedMonth !== '') {
            $notes = collect($notes)
                ->filter(fn (array $note) => $extractMonth((string) ($note['llamar'] ?? '')) === $selectedMonth)
                ->values()
                ->all();
        }

        if (empty($notes)) {
            return back()->withErrors([
                'notes' => $selectedMonth !== ''
                    ? 'No hay notas para el mes seleccionado.'
                    : 'Agrega al menos una nota con contenido para exportar.',
            ])->withInput();
        }

        $monthIndex = array_flip($monthOrder);
        $foundMonths = collect($notes)
            ->pluck('llamar')
            ->map(fn ($value) => $extractMonth((string) $value))
            ->filter()
            ->unique()
            ->sortBy(fn ($month) => $monthIndex[$month] ?? 99)
            ->values()
            ->all();

        if (empty($foundMonths)) {
            $foundMonths = [$selectedMonth !== '' ? $selectedMonth : now()->locale('es')->translatedFormat('F')];
        }

        $exportMonthTitle = (count($foundMonths) > 1 ? 'MESES: ' : 'MES: ')
            .implode(', ', array_map('mb_strtoupper', $foundMonths));

        $fileName = 'notas_'.\Illuminate\Support\Str::slug(implode('-', $foundMonths)).'.pdf';
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('quotations.notes-pdf', [
            'notes' => $notes,
            'exportMonthTitle' => $exportMonthTitle,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_company' => 'required|string|max:255',
            'client_ruc' => 'nullable|string|max:20',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'items' => 'nullable|array',
            'items.*.service_name' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.image_path' => 'nullable|string',
            'apply_igv' => 'boolean',
        ]);

        $subtotal = 0;
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $quantity = $item['quantity'] ?? 1;
                $price = $item['price'] ?? 0;
                $subtotal += $quantity * $price;
            }
        }

        $igv = $request->has('apply_igv') ? $subtotal * 0.18 : 0;
        $total = $subtotal + $igv;

        $quotation = Quotation::create([
            'user_id' => Auth::id(),
            'client_name' => $validated['client_company'], // Use company name as client name
            'client_company' => $validated['client_company'],
            'client_ruc' => $validated['client_ruc'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'client_email' => $validated['client_email'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'date' => $validated['date'] ?? now()->format('Y-m-d'),
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
        ]);

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                if (! empty($item['service_name'])) {
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'service_name' => $item['service_name'],
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['quantity'] ?? 1) * ($item['price'] ?? 0),
                        'image_path' => $item['image_path'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('quotations.show', $quotation);
    }

    public function show(Quotation $quotation)
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        return view('quotations.show', compact('quotation', 'settings'));
    }

    public function index(Request $request)
    {
        $query = Quotation::with('items');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_ruc', 'like', "%{$search}%")
                    ->orWhere('client_phone', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%")
                    ->orWhere('client_address', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('service_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('ruc')) {
            $query->where('client_ruc', 'like', '%'.$request->ruc.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('message')) {
            $query->where('follow_up_message', $request->message);
        }

        if ($request->filled('service')) {
            $service = trim((string) $request->service);
            $query->whereHas('items', function ($itemQuery) use ($service) {
                $itemQuery->where('service_name', 'like', "%{$service}%");
            });
        }

        if ($request->filled('price_min')) {
            $query->where('total', '>=', (float) $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('total', '<=', (float) $request->price_max);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('response_date_from')) {
            $query->whereDate('response_date', '>=', $request->response_date_from);
        }

        if ($request->filled('response_date_to')) {
            $query->whereDate('response_date', '<=', $request->response_date_to);
        }

        $quotations = $query->latest()->paginate(20)->withQueryString();
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $customMessages = \App\Models\PredefinedMessage::where('is_active', true)->orderBy('created_at', 'desc')->get();

        return view('quotations.index', compact('quotations', 'settings', 'customMessages'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'response_date' => 'nullable|date',
            'follow_up_message' => 'nullable|string',
            'follow_up_note' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if (array_key_exists('status', $validated)) {
            $rawStatus = strtolower(trim((string) $validated['status']));
            $statusMap = [
                '' => 'pending',
                'pendiente' => 'pending',
                'pending' => 'pending',
                'en proceso' => 'in_process',
                'en_proceso' => 'in_process',
                'in process' => 'in_process',
                'in_process' => 'in_process',
                'completado' => 'completed',
                'completada' => 'completed',
                'completed' => 'completed',
                'confirmado' => 'confirmed',
                'confirmada' => 'confirmed',
                'confirmed' => 'confirmed',
                'denegado' => 'denied',
                'denied' => 'denied',
                'llamadas' => 'calls',
                'calls' => 'calls',
                'multiples llamadas' => 'multiple_calls',
                'multiple_calls' => 'multiple_calls',
                'comunicacion pendiente' => 'pending_communication',
                'pending_communication' => 'pending_communication',
            ];
            $validated['status'] = $statusMap[$rawStatus] ?? 'pending';
        }

        $quotation->update($validated);

        return response()->json(['success' => true]);
    }

    public function downloadPDF(Quotation $quotation)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('quotations.pdf', compact('quotation'));

        return $pdf->download('COT-'.strtoupper(\Illuminate\Support\Str::slug($quotation->client_company ?? $quotation->client_name)).'-'.$quotation->date.'.pdf');
    }

    public function publicShow($slug)
    {
        $quotation = Quotation::where('slug', $slug)->firstOrFail();

        return $this->downloadPDF($quotation);
    }

    public function sendEmail(Request $request, Quotation $quotation)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf',
        ]);

        // 1. Determine Message Body
        $type = $request->input('type', 'initial');
        $settingKey = null;

        switch ($type) {
            case 'Confirmación': $settingKey = 'confirmation_email_message';
                break;
            case 'Servicio': $settingKey = 'service_email_message';
                break;
            case 'Acceso de su servicio': $settingKey = 'access_email_message';
                break;
            case 'resend': $settingKey = 'resend_email_message';
                break;
            case 'initial': $settingKey = 'quotation_email_message';
                break;
        }

        $body = '';
        if ($settingKey) {
            $body = \App\Models\Setting::where('key', $settingKey)->value('value');
        } else {
            // It might be a custom message
            $customMessage = \App\Models\PredefinedMessage::where('title', $type)->first();
            if ($customMessage) {
                $body = $customMessage->content;
            }
        }

        // 2. Variable Replacement
        if ($body) {
            $replacements = [
                '[Nombre]' => $quotation->client_name ?? $quotation->client_company,
                '[Empresa]' => $quotation->client_company ?? '',
                '[RUC]' => $quotation->client_ruc ?? '',
                '[Fecha]' => \Carbon\Carbon::parse($quotation->date)->format('d/m/Y'),
                '[Servicio]' => $quotation->items->first()->service_name ?? '',
                '[Total]' => number_format($quotation->total, 2),
                '[Telefono]' => $quotation->client_phone ?? '',
                '[Email]' => $quotation->client_email ?? '',
                '[Direccion]' => $quotation->client_address ?? '',
                '[Link]' => $quotation->slug ? route('quotations.public', $quotation->slug) : \Illuminate\Support\Facades\URL::signedRoute('quotations.download', $quotation),
            ];

            foreach ($replacements as $key => $value) {
                $body = str_replace($key, $value, $body);
            }
        }

        // 3. Handle PDF (Upload vs Generation)
        $tempPath = null;

        if ($request->hasFile('pdf_file')) {
            // Use uploaded file (from show.blade.php)
            $pdfFile = $request->file('pdf_file');
            $filename = 'quotation-'.$quotation->id.'.pdf';
            $directory = storage_path('app/temp_quotations');
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $pdfFile->move($directory, $filename);
            $tempPath = $directory.DIRECTORY_SEPARATOR.$filename;
        } else {
            // Generate server-side (from index.blade.php)
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('quotations.pdf', compact('quotation'));
            $pdfOutput = $pdf->output();
            $tempPath = storage_path('app/public/temp_quotation_'.$quotation->id.'.pdf');
            file_put_contents($tempPath, $pdfOutput);
        }

        try {
            // 4. Send Email
            \Illuminate\Support\Facades\Mail::to($request->input('email'))
                ->send(new \App\Mail\QuotationMail($quotation, $tempPath, $body)); // Pass path, not output, if Mail expects path?
            // Wait, QuotationMail constructor I saw earlier expected $pdfOutput (raw data) or path?
            // Let's check QuotationMail.
            // Assuming I need to check QuotationMail.
            // For now, I'll pass $tempPath and ensure QuotationMail handles it.
            // Actually, standard Laravel attach uses path.

            // Delete temp file
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Correo enviado correctamente']);
            }

            return back()->with('success', 'Cotización enviada por correo correctamente.');

        } catch (\Exception $e) {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Error al enviar correo: '.$e->getMessage());
        }
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Cotización eliminada correctamente.');
    }

    public function getReminders()
    {
        $quotations = Quotation::query()
            ->whereNotNull('response_date')
            ->orWhereNotNull('follow_up_note')
            ->get();
        $today = now()->format('Y-m-d');

        $reminders = [];

        foreach ($quotations as $quotation) {
            $isDue = false;
            $reason = '';

            // Check Response Date
            if ($quotation->response_date && str_starts_with($quotation->response_date, $today)) {
                $isDue = true;
                $reason = 'Fecha de respuesta programada para hoy.';
            }

            // Check Note
            if (! $isDue && $quotation->follow_up_note) {
                // Regex for dd/mm or dd-mm
                if (preg_match_all('/\b(\d{1,2})[\/.-](\d{1,2})(?:[\/.-](\d{2,4}))?\b/', $quotation->follow_up_note, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $day = str_pad($match[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($match[2], 2, '0', STR_PAD_LEFT);
                        if ($day === now()->format('d') && $month === now()->format('m')) {
                            $isDue = true;
                            $reason = 'Nota menciona la fecha de hoy.';
                            break;
                        }
                    }
                }
            }

            if ($isDue) {
                $phone = preg_replace('/\D/', '', $quotation->client_phone);
                if (! str_starts_with($phone, '51') && strlen($phone) === 9) {
                    $phone = '51'.$phone;
                }

                $reminders[] = [
                    'id' => $quotation->id,
                    'client' => $quotation->client_company ?? $quotation->client_name,
                    'reason' => $reason,
                    'note' => $quotation->follow_up_note,
                    'phone' => $phone,
                    'date' => $quotation->response_date,
                    'link' => route('quotations.show', $quotation),
                ];
            }
        }

        return response()->json($reminders);
    }

    public function export(Request $request)
    {
        $columnNames = [
            'id' => 'ID',
            'empresa' => 'Empresa/Cliente',
            'contacto' => 'Contacto',
            'email' => 'Email',
            'telefono' => 'Teléfono',
            'ruc' => 'RUC',
            'direccion' => 'Dirección',
            'servicio' => 'Servicio Principal',
            'descripcion' => 'Descripción',
            'total' => 'Total',
            'fecha_envio' => 'Fecha Envío',
            'fecha_respuesta' => 'Fecha Respuesta',
            'mensaje' => 'Mensaje',
            'nota' => 'Nota',
            'usuario' => 'Usuario',
            'estado' => 'Estado',
            'eliminado' => 'Eliminado',
        ];

        $selectedColumns = collect((array) $request->input('columns', array_keys($columnNames)))
            ->filter(fn ($column) => array_key_exists((string) $column, $columnNames))
            ->values()
            ->all();

        if (empty($selectedColumns)) {
            $selectedColumns = array_keys($columnNames);
        }

        $query = Quotation::with(['items', 'user']);

        // Include soft deleted only when they are relevant to requested columns.
        if (in_array('eliminado', $selectedColumns, true) || in_array('estado', $selectedColumns, true)) {
            $query->withTrashed();
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('message')) {
            $query->where('follow_up_message', $request->message);
        }
        if ($request->filled('client')) {
            $client = $request->client;
            $query->where(function ($q) use ($client) {
                $q->where('client_name', 'like', "%{$client}%")
                    ->orWhere('client_company', 'like', "%{$client}%")
                    ->orWhere('id', 'like', "%{$client}%");
            });
        }

        $quotations = $query->latest()->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $statusLabel = static function (?string $status): string {
            $normalized = strtolower(trim((string) $status));

            return match ($normalized) {
                'in_process', 'en_proceso', 'en proceso' => 'En Proceso',
                'completed', 'completado', 'completada' => 'Completado',
                'confirmed', 'confirmado', 'confirmada' => 'Confirmado',
                default => 'Pendiente',
            };
        };

        // Headers
        $col = 'A';
        foreach ($selectedColumns as $columnKey) {
            if (isset($columnNames[$columnKey])) {
                $sheet->setCellValue($col.'1', $columnNames[$columnKey]);
                $col++;
            }
        }

        // Data
        $row = 2;
        foreach ($quotations as $quotation) {
            $col = 'A';
            foreach ($selectedColumns as $columnKey) {
                $value = '';
                switch ($columnKey) {
                    case 'id': $value = $quotation->id;
                        break;
                    case 'empresa': $value = $quotation->client_company ?? $quotation->client_name;
                        break;
                    case 'contacto': $value = $quotation->client_name;
                        break;
                    case 'email': $value = $quotation->client_email;
                        break;
                    case 'telefono': $value = $quotation->client_phone;
                        break;
                    case 'ruc': $value = $quotation->client_ruc;
                        break;
                    case 'direccion': $value = $quotation->client_address;
                        break;
                    case 'servicio': $value = $quotation->items->first()->service_name ?? '';
                        break;
                    case 'descripcion': $value = $quotation->items->pluck('service_name')->implode(', ');
                        break;
                    case 'total': $value = 'S/ '.number_format((float) $quotation->total, 2);
                        break;
                    case 'fecha_envio': $value = $quotation->date ? \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') : '';
                        break;
                    case 'fecha_respuesta': $value = $quotation->response_date ? \Carbon\Carbon::parse($quotation->response_date)->format('d/m/Y') : '';
                        break;
                    case 'mensaje': $value = $quotation->follow_up_message;
                        break;
                    case 'nota': $value = $quotation->follow_up_note;
                        break;
                    case 'usuario': $value = $quotation->user->name ?? 'N/A';
                        break;
                    case 'estado':
                        $value = $quotation->deleted_at ? 'Eliminado' : $statusLabel($quotation->status);
                        break;
                    case 'eliminado': $value = $quotation->deleted_at ? $quotation->deleted_at->format('Y-m-d H:i:s') : '';
                        break;
                }
                $sheet->setCellValue($col.$row, $value);
                $col++;
            }
            $row++;
        }

        // Style headers (purple gradient feel)
        $lastCol = $sheet->getHighestColumn();
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5F1BF2'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'cotizaciones_'.now()->format('Y-m-d_H-i').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportWord(Request $request)
    {
        $query = Quotation::with(['items', 'user']);

        // Check if we should include soft deleted items
        if (in_array('eliminado', $request->input('columns', [])) || in_array('estado', $request->input('columns', []))) {
            $query->withTrashed();
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('message')) {
            $query->where('follow_up_message', $request->message);
        }
        if ($request->filled('client')) {
            $client = $request->client;
            $query->where(function ($q) use ($client) {
                $q->where('client_name', 'like', "%{$client}%")
                    ->orWhere('client_company', 'like', "%{$client}%")
                    ->orWhere('id', 'like', "%{$client}%");
            });
        }

        $quotations = $query->latest()->get();

        // Column Mapping
        $columnNames = [
            'id' => 'ID',
            'empresa' => 'Empresa/Cliente',
            'contacto' => 'Contacto',
            'email' => 'Email',
            'telefono' => 'Teléfono',
            'ruc' => 'RUC',
            'direccion' => 'Dirección',
            'servicio' => 'Servicio Principal',
            'descripcion' => 'Descripción',
            'total' => 'Total',
            'fecha_envio' => 'Fecha Envío',
            'fecha_respuesta' => 'Fecha Respuesta',
            'mensaje' => 'Mensaje',
            'nota' => 'Nota',
            'usuario' => 'Usuario',
            'estado' => 'Estado',
            'eliminado' => 'Eliminado',
        ];

        $selectedColumns = $request->input('columns', array_keys($columnNames));

        // Generate Report Title based on date range
        $reportTitle = 'REPORTE GENERAL';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $start = \Carbon\Carbon::parse($request->date_from);
            $end = \Carbon\Carbon::parse($request->date_to);

            if ($start->format('d') == '01' && $end->format('d') == $end->copy()->endOfMonth()->format('d') && $start->month == $end->month && $start->year == $end->year) {
                // Full month selected
                $months = [
                    1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO',
                    7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE',
                ];
                $reportTitle = 'REPORTE MES DE '.$months[$start->month].' '.$start->year;
            } else {
                // Custom range
                $reportTitle = 'DEL '.$start->format('d/m/Y').' AL '.$end->format('d/m/Y');
            }
        } elseif ($request->filled('date_from')) {
            $reportTitle = 'DESDE '.\Carbon\Carbon::parse($request->date_from)->format('d/m/Y');
        } elseif ($request->filled('date_to')) {
            $reportTitle = 'HASTA '.\Carbon\Carbon::parse($request->date_to)->format('d/m/Y');
        }

        $fileName = 'cotizaciones_'.now()->format('Y-m-d_H-i').'.doc';

        return response(view('quotations.word', compact('quotations', 'selectedColumns', 'columnNames', 'reportTitle')))
            ->header('Content-Type', 'application/vnd.ms-word')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }
}
