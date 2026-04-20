<?php

namespace App\Http\Controllers;

use App\Models\ServiceImage;
use App\Models\ServiceMapping;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        // Default Dashboard WhatsApp Templates
        if (!isset($settings['wa_activation'])) $settings['wa_activation'] = "Hola [Nombre],\n\nSu servicio asociado al dominio *[Dominio]* ha sido activado exitosamente.\n\n¡Gracias por confiar en nosotros!";
        if (!isset($settings['wa_expiry'])) $settings['wa_expiry'] = "Hola [Nombre],\n\nLe recordamos que su dominio *[Dominio]* está próximo a vencer.\n\nPor favor, confirme si desea renovarlo para evitar interrupciones.\n\nQuedamos atentos.";
        if (!isset($settings['wa_promo'])) $settings['wa_promo'] = "Estimado/a [Nombre],\n\nLe informamos que tiene un saldo pendiente para la renovación de *[Dominio]*.\n\nPor favor, envíenos su comprobante una vez realizado el pago.\n\n¡Gracias!";

        $images = ServiceImage::orderBy('order')->get();
        foreach ($images as $image) {
            $this->ensurePublicStorageMirror($image->path ?: ('service_images/' . $image->filename));
        }
        $services = $this->getAvailableServices();
        $mappings = ServiceMapping::with('serviceImage')->orderBy('service_name')->orderBy('order')->get();
        // Fetch PredefinedMessages instead of CustomMessages
        // Filter those that are likely "custom" (created by user) or just show all
        // Given the migration merged them, we show all active ones, or just all.
        $allMessages = \App\Models\PredefinedMessage::orderBy('created_at', 'desc')->get();
        // Categorize messages (default to general if column not yet added due to sandbox issues)
        $msgByUsage = [
            'quotation' => $allMessages->filter(fn($m) => ($m->usage ?? 'general') === 'quotation'),
            'dashboard' => $allMessages->filter(fn($m) => ($m->usage ?? 'general') === 'dashboard'),
            'general' => $allMessages->filter(fn($m) => ($m->usage ?? 'general') === 'general' || !isset($m->usage)),
        ];

        return view('settings.index', compact('settings', 'images', 'services', 'mappings', 'msgByUsage'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Ajustes actualizados correctamente.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max each
            'name' => 'nullable|string|max:255',
            'auto_service_name' => 'nullable|string|max:255',
            'auto_service_name_custom' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $successCount = 0;

            $serviceName = trim((string) $request->input('auto_service_name'));
            if ($serviceName === 'OTRO') {
                $serviceName = trim((string) $request->input('auto_service_name_custom'));
            }

            foreach ($files as $index => $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                
                // Store in storage/app/public/service_images
                $path = $file->storeAs('service_images', $filename, 'public');
                
                // Keep a physical copy in public/storage for environments without symlink.
                $this->ensurePublicStorageMirror($path);

                // Naming logic for multiple files
                if ($request->filled('name')) {
                    $displayName = count($files) > 1 
                        ? $request->name . ' (' . ($index + 1) . ')' 
                        : $request->name;
                } else {
                    $displayName = $originalName;
                }

                $image = ServiceImage::create([
                    'name' => $displayName,
                    'filename' => $filename,
                    'path' => $path,
                    'is_active' => true,
                    'order' => ServiceImage::max('order') + 1,
                ]);

                // Auto-map if service was selected
                if (!empty($serviceName)) {
                    ServiceMapping::create([
                        'service_name' => $serviceName,
                        'service_image_id' => $image->id,
                        'order' => ServiceMapping::where('service_name', $serviceName)->count(),
                    ]);
                }

                $successCount++;
            }

            return back()->with('success', "$successCount imagen(es) subida(s) correctamente.");
        }

        return back()->with('error', 'No se pudieron subir las imágenes.');
    }

    public function deleteImage(ServiceImage $image)
    {
        // Check if image is used in mappings
        if ($image->serviceMappings()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la imagen porque está asignada a uno o más servicios. Elimina las asignaciones primero.');
        }

        // Delete file from storage
        Storage::disk('public')->delete($image->path);
        $publicPath = public_path('storage/' . ltrim($image->path ?: ('service_images/' . $image->filename), '/'));
        if (is_file($publicPath)) {
            @unlink($publicPath);
        }

        // Delete record
        $image->delete();

        return back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function updateMapping(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_name_custom' => 'nullable|string|max:255',
            'service_image_id' => 'required|exists:service_images,id',
        ]);

        $serviceName = trim((string) $request->input('service_name'));
        if (strtoupper($serviceName) === 'OTRO') {
            $serviceName = trim((string) $request->input('service_name_custom'));
        }

        if ($serviceName === '') {
            return back()
                ->withErrors(['service_name_custom' => 'Escribe el nombre del servicio cuando selecciones OTRO.'])
                ->withInput();
        }

        // Create new mapping
        ServiceMapping::create([
            'service_name' => $serviceName,
            'service_image_id' => $request->service_image_id,
            'order' => ServiceMapping::where('service_name', $serviceName)->count(),
        ]);

        return back()->with('success', 'Imagen asignada al servicio correctamente.');
    }

    public function deleteMapping(ServiceMapping $mapping)
    {
        $mapping->delete();

        return back()->with('success', 'Asignación eliminada correctamente.');
    }

    public function storeCustomMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:email,whatsapp,both',
            'usage' => 'nullable|string|in:quotation,dashboard,general',
        ]);

        // Calculate next number
        $maxNumber = \App\Models\PredefinedMessage::max('number') ?? 100;

        \App\Models\PredefinedMessage::create([
            'number' => $maxNumber + 1,
            'title' => $request->name,
            'content' => $request->input('content'),
            'type' => $request->type === 'both' ? 'whatsapp' : $request->type,
            'usage' => $request->input('usage', 'general'),
            'is_active' => true,
            'is_favorite' => true, // Auto-favorite user created templates
        ]);

        return back()->with('success', 'Plantilla guardada correctamente.');
    }

    public function updateCustomMessage(Request $request, \App\Models\PredefinedMessage $customMessage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:email,whatsapp,both',
            'usage' => 'nullable|string|in:quotation,dashboard,general',
        ]);

        $customMessage->update([
            'title' => $request->name,
            'content' => $request->input('content'),
            'type' => $request->type,
            'usage' => $request->input('usage', 'general'),
        ]);

        return back()->with('success', 'Mensaje actualizado correctamente.');
    }

    public function deleteCustomMessage(\App\Models\PredefinedMessage $customMessage)
    {
        $customMessage->delete();

        return back()->with('success', 'Mensaje eliminado correctamente.');
    }

    private function getAvailableServices()
    {
        return [
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
            'OTRO',
        ];
    }

    private function ensurePublicStorageMirror(string $relativePath): void
    {
        $relativePath = ltrim($relativePath, '/');
        $source = storage_path('app/public/' . $relativePath);
        $target = public_path('storage/' . $relativePath);

        if (!is_file($source) || is_file($target)) {
            return;
        }

        $targetDir = dirname($target);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        @copy($source, $target);
    }
}
