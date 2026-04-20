<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $images = [
            ['name' => 'Web Informativa', 'file' => 'web-informativa.png', 'services' => ['WEB INFORMATIVA', 'WEB FUSION E-COMMERCE', 'WEB FUSION AULA VIRTUAL']],
            ['name' => 'Web E-Commerce', 'file' => 'E-COMERCE.png', 'services' => ['WEB E-COMMERCE', 'WEB FUSION E-COMMERCE']],
            ['name' => 'Aula Virtual', 'file' => 'aula-virtual.png', 'services' => ['WEB AULA VIRTUAL', 'WEB FUSION AULA VIRTUAL']],
            ['name' => 'Posicionamiento SEO', 'file' => 'posicionamiento-seo.png', 'services' => ['POSICIONAMIENTO SEO']],
            ['name' => 'Yoast SEO', 'file' => 'yoast-seo.png', 'services' => ['PLUGIN YOAST SEO']],
            ['name' => 'Reestructuración', 'file' => 'restructuracion.png', 'services' => ['RESTRUCTURACION BASICA', 'RESTRUCTURACION E-COMMERCE']],
            ['name' => 'Redes Sociales', 'file' => 'REDES.png', 'services' => ['REDES SOCIALES']],
        ];

        // Ensure directory exists
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('service_images');

        foreach ($images as $index => $img) {
            $sourcePath = public_path('images/'.$img['file']);

            if (file_exists($sourcePath)) {
                // Copy to storage
                $targetFilename = $img['file'];
                \Illuminate\Support\Facades\Storage::disk('public')->put('service_images/'.$targetFilename, file_get_contents($sourcePath));

                // Create Image Record
                $serviceImage = \App\Models\ServiceImage::firstOrCreate(
                    ['filename' => $targetFilename],
                    [
                        'name' => $img['name'],
                        'path' => 'service_images/'.$targetFilename,
                        'is_active' => true,
                        'order' => $index + 1,
                    ]
                );

                // Create Mappings
                foreach ($img['services'] as $serviceName) {
                    \App\Models\ServiceMapping::firstOrCreate([
                        'service_name' => $serviceName,
                        'service_image_id' => $serviceImage->id,
                    ], [
                        'order' => 1,
                    ]);
                }
            }
        }
    }
}
