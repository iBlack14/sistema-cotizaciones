<?php

namespace App\Exports;

use App\Models\Quotation;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuotationsExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Incluir TODAS las cotizaciones, incluso las eliminadas
        $query = Quotation::withTrashed()->with(['items', 'user']);

        // Aplicar filtros
        if (! empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }

        if (! empty($this->filters['message'])) {
            $query->where('follow_up_message', $this->filters['message']);
        }

        if (! empty($this->filters['client'])) {
            $query->where(function ($q) {
                $search = $this->filters['client'];
                $q->where('id', 'like', '%'.$search.'%')
                    ->orWhere('client_name', 'like', '%'.$search.'%')
                    ->orWhere('client_company', 'like', '%'.$search.'%')
                    ->orWhere('client_ruc', 'like', '%'.$search.'%')
                    ->orWhere('client_email', 'like', '%'.$search.'%')
                    ->orWhere('client_phone', 'like', '%'.$search.'%');
            });
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        $allHeadings = [
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

        // Si no se especifican columnas, exportar todas
        if (empty($this->filters['columns'])) {
            return array_values($allHeadings);
        }

        // Solo exportar columnas seleccionadas
        $selectedHeadings = [];
        foreach ($this->filters['columns'] as $column) {
            if (isset($allHeadings[$column])) {
                $selectedHeadings[] = $allHeadings[$column];
            }
        }

        return $selectedHeadings;
    }

    /**
     * @param  mixed  $quotation
     */
    public function map($quotation): array
    {
        $allData = [
            'id' => $quotation->id,
            'empresa' => $quotation->client_company ?? $quotation->client_name,
            'contacto' => $quotation->client_name,
            'email' => $quotation->client_email,
            'telefono' => $quotation->client_phone,
            'ruc' => $quotation->client_ruc,
            'direccion' => $quotation->client_address,
            'servicio' => $quotation->items->first()->service_name ?? 'N/A',
            'descripcion' => $quotation->items->first()->description ?? 'N/A',
            'total' => 'S/ '.number_format($quotation->total, 2),
            'fecha_envio' => Carbon::parse($quotation->date)->format('d/m/Y'),
            'fecha_respuesta' => $quotation->response_date ? Carbon::parse($quotation->response_date)->format('d/m/Y') : 'Pendiente',
            'mensaje' => $quotation->follow_up_message ?? '-',
            'nota' => $quotation->follow_up_note ?? '-',
            'usuario' => $quotation->user->name ?? 'N/A',
            'estado' => $this->getStatus($quotation),
            'eliminado' => $quotation->deleted_at ? 'Sí' : 'No',
        ];

        // Si no se especifican columnas, exportar todas
        if (empty($this->filters['columns'])) {
            return array_values($allData);
        }

        // Solo exportar columnas seleccionadas
        $selectedData = [];
        foreach ($this->filters['columns'] as $column) {
            if (isset($allData[$column])) {
                $selectedData[] = $allData[$column];
            }
        }

        return $selectedData;
    }

    /**
     * Determinar el estado de la cotización
     */
    private function getStatus($quotation)
    {
        if ($quotation->response_date) {
            return 'Respondido';
        } elseif ($quotation->follow_up_message) {
            return 'En seguimiento';
        } else {
            return 'Enviado';
        }
    }

    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo del header (primera fila)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8704BF'], // Color morado
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Anchos de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 25, // Empresa
            'C' => 20, // Contacto
            'D' => 30, // Email
            'E' => 15, // Teléfono
            'F' => 15, // RUC
            'G' => 35, // Dirección
            'H' => 25, // Servicio
            'I' => 40, // Descripción
            'J' => 12, // Total
            'K' => 15, // Fecha Envío
            'L' => 15, // Fecha Respuesta
            'M' => 20, // Mensaje
            'N' => 30, // Nota
            'O' => 20, // Usuario
            'P' => 15, // Estado
            'Q' => 12, // Eliminado
        ];
    }
}
