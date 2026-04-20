<?php

namespace App\Http\Controllers;

use App\Imports\DomainsImport;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Domain::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'activo':
                    $query->active();
                    break;
                case 'expirado':
                    $query->expired();
                    break;
                case 'por_vencer':
                    $query->expiringSoon();
                    break;
            }
        }

        $domains = $query->orderBy('expiration_date', 'asc')->paginate(12);

        // Calculate statistics
        $stats = [
            'total' => Domain::count(),
            'active' => Domain::active()->count(),
            'expiring' => Domain::expiringSoon()->count(),
            'expired' => Domain::expired()->count(),
        ];

        // ALL Domains for Email Selection Modal
        $allDomains = Domain::select('id', 'domain_name', 'emails', 'corporate_emails')->orderBy('domain_name')->get();

        // Load predefined messages for WhatsApp modal (Dashboard or General usage)
        $customMessages = \App\Models\PredefinedMessage::whereIn('type', ['whatsapp', 'both'])
            ->whereIn('usage', ['dashboard', 'general'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Load settings for WhatsApp templates
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        if ($request->ajax()) {
            return view('domains.partials.list', compact('domains', 'stats', 'customMessages'));
        }

        return view('domains.index', compact('domains', 'stats', 'allDomains', 'customMessages', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('domains.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'domain_name' => 'required|string|max:255|unique:domains',
            'registration_date' => 'required|date',
            'expiration_date' => 'required|date|after:registration_date',
            'auto_renew' => 'boolean',
            'status' => 'required|in:activo,expirado,pendiente,suspendido',
            'price' => 'required|numeric|min:0',
            'hosting_info' => 'nullable|string',
            'dns_servers' => 'nullable|string',
            'notes' => 'nullable|string',
            'plugins' => 'nullable|string',
            'licenses' => 'nullable|string',
            'maintenance_status' => 'required|in:activo,inactivo',
            'corporate_emails' => 'nullable|string',
            'emails' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        $validated['user_id'] = Auth::id();
        $validated['auto_renew'] = $request->has('auto_renew');

        Domain::create($validated);

        return redirect()->route('domains.index')
            ->with('success', 'Dominio registrado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain)
    {
        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domain $domain)
    {
        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'domain_name' => 'required|string|max:255|unique:domains,domain_name,'.$domain->id,
            'registration_date' => 'required|date',
            'expiration_date' => 'required|date|after:registration_date',
            'auto_renew' => 'boolean',
            'status' => 'required|in:activo,expirado,pendiente,suspendido',
            'price' => 'required|numeric|min:0',
            'hosting_info' => 'nullable|string',
            'dns_servers' => 'nullable|string',
            'notes' => 'nullable|string',
            'plugins' => 'nullable|string',
            'licenses' => 'nullable|string',
            'maintenance_status' => 'required|in:activo,inactivo',
            'corporate_emails' => 'nullable|string',
            'emails' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        $validated['auto_renew'] = $request->has('auto_renew');

        // Keep original owner if exists; fallback to current user for legacy rows without owner.
        $validated['user_id'] = $domain->user_id ?? Auth::id();

        $domain->update($validated);

        return redirect()->route('domains.index')
            ->with('success', 'Dominio actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('domains.index')
            ->with('success', 'Dominio eliminado exitosamente');
    }

    /**
     * Import domains from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $import = new DomainsImport(Auth::id());
            Excel::import($import, $request->file('file'));

            if ($import->hasErrors()) {
                $errorMessage = "Se importaron {$import->getImportedCount()} dominios. Errores encontrados:\n";
                $errorMessage .= implode("\n", array_slice($import->getErrors(), 0, 10));

                if (count($import->getErrors()) > 10) {
                    $errorMessage .= "\n... y ".(count($import->getErrors()) - 10).' errores más.';
                }

                return redirect()->route('domains.index')
                    ->with('warning', $errorMessage);
            }

            return redirect()->route('domains.index')
                ->with('success', "{$import->getImportedCount()} dominios importados correctamente");
        } catch (\Exception $e) {
            return redirect()->route('domains.index')
                ->with('error', 'Error al importar el archivo: '.$e->getMessage());
        }
    }

    /**
     * Download Excel template for importing domains.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Cliente',
            'Dominio',
            'Fecha Registro',
            'Fecha Vencimiento',
            'Estado',
            'Precio',
            'Auto Renovar',
            'Estado Mantenimiento',
            'Hosting Info',
            'DNS Servers',
            'Notes',
            'Plugins',
            'Licenses',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add example row
        $exampleData = [
            'Empresa XYZ',
            'ejemplo.com',
            '2024-01-01',
            '2025-01-01',
            'activo',
            '150.00',
            'si',
            'activo',
            'Servidor 1 - IP: 192.168.1.1',
            'ns1.ejemplo.com, ns2.ejemplo.com',
            'Notas del dominio',
            'WooCommerce, Elementor',
            'Licencia Premium',
        ];

        $sheet->fromArray($exampleData, null, 'A2');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '5F1BF2']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'plantilla_dominios.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export all domains (domain_name + phone) to Excel.
     */
    public function export()
    {
        $domains = Domain::select('domain_name', 'phone')->orderBy('domain_name')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Dominio', 'Numero'];
        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $row = 2;
        foreach ($domains as $domain) {
            $sheet->setCellValue('A'.$row, $domain->domain_name);
            $sheet->setCellValue('B'.$row, $domain->phone ?? '');
            $row++;
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '5F1BF2']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'exportacion_dominios_'.now()->format('Y-m-d_H-i').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export domains to Word (.doc) respecting active filters.
     */
    public function exportWord(Request $request)
    {
        // Skip view existence check to avoid lint or runtime errors in environments with strict method checks
        $query = Domain::with('user');

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'activo':     $query->active();
                    break;
                case 'expirado':   $query->expired();
                    break;
                case 'por_vencer': $query->expiringSoon();
                    break;
            }
        }

        $domains = $query->orderBy('expiration_date', 'asc')->get();

        // Build title based on active filters
        $title = 'REPORTE GENERAL DE DOMINIOS';
        if ($request->filled('status')) {
            $labels = ['activo' => 'ACTIVOS', 'expirado' => 'EXPIRADOS', 'por_vencer' => 'POR VENCER'];
            $title = 'DOMINIOS '.($labels[$request->status] ?? strtoupper($request->status));
        }
        if ($request->filled('search')) {
            $title .= ' — Búsqueda: "'.$request->search.'"';
        }

        $fileName = 'dominios_'.now()->format('Y-m-d_H-i').'.doc';

        return response(view('domains.word', compact('domains', 'title')))
            ->header('Content-Type', 'application/vnd.ms-word')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }
}
