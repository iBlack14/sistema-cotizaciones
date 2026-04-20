<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [App\Http\Controllers\DomainController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/quotations/{quotation}/download', [QuotationController::class, 'downloadPDF'])
    ->name('quotations.download')
    ->middleware('signed');

Route::get('/cotizacion/{slug}', [QuotationController::class, 'publicShow'])
    ->name('quotations.public');

// Endpoint de emergencia para ejecutar migraciones sin terminal
// Uso: /system/migrate?key=TU_CLAVE
Route::get('/system/migrate', function () {
    $configuredKey = (string) env('SYSTEM_MAINTENANCE_KEY', '');
    $providedKey = (string) request()->query('key', '');

    if ($configuredKey === '' || $providedKey === '' || !hash_equals($configuredKey, $providedKey)) {
        return response()->json([
            'success' => false,
            'error' => 'Unauthorized',
        ], 403);
    }

    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('optimize:clear');
        $clearOutput = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Migraciones ejecutadas correctamente.',
            'migrate_output' => trim($migrateOutput),
            'clear_output' => trim($clearOutput),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
})->middleware('throttle:3,1');

Route::middleware('auth')->group(function () {
    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Rutas de correo masivo
    Route::prefix('emails')->group(function () {
        Route::get('/compose', [App\Http\Controllers\EmailController::class, 'showEmailForm'])->name('emails.compose');
        Route::post('/send', [App\Http\Controllers\EmailController::class, 'sendBulkEmail'])->name('emails.send');
    });
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/quotations/notes', [QuotationController::class, 'notes'])->name('quotations.notes');
    Route::post('/quotations/notes/save', [QuotationController::class, 'saveNotes'])->name('quotations.notes.save');
    Route::post('/quotations/notes/export-word', [QuotationController::class, 'exportNotesWord'])->name('quotations.notes.export-word');
    Route::match(['GET', 'POST'], '/quotations/notes/export-pdf', [QuotationController::class, 'exportNotesPdf'])->name('quotations.notes.export-pdf');
    Route::resource('quotations', QuotationController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    Route::post('/quotations/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('quotations.send-email');
    Route::get('/quotations-export', [QuotationController::class, 'export'])->name('quotations.export');
    Route::get('/quotations-export-word', [QuotationController::class, 'exportWord'])->name('quotations.export-word');

    // Domains Management
    Route::resource('domains', App\Http\Controllers\DomainController::class);
    Route::post('/domains/import', [App\Http\Controllers\DomainController::class, 'import'])->name('domains.import');
    Route::get('/domains/import-template', [App\Http\Controllers\DomainController::class, 'downloadTemplate'])->name('domains.import-template');
    Route::get('/domains-export', [App\Http\Controllers\DomainController::class, 'export'])->name('domains.export');
    Route::get('/domains-export-word', [App\Http\Controllers\DomainController::class, 'exportWord'])->name('domains.export-word');

    // Rutas para envío de correos de dominios
    Route::prefix('domains/{domain}')->group(function () {
        Route::get('/email', [App\Http\Controllers\DomainEmailController::class, 'showEmailForm'])->name('domains.email.form');
        Route::post('/email/send', [App\Http\Controllers\DomainEmailController::class, 'sendEmail'])->name('domains.email.send');
    });

    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/images', [App\Http\Controllers\SettingController::class, 'uploadImage'])->name('settings.images.upload');
    Route::delete('/settings/images/{image}', [App\Http\Controllers\SettingController::class, 'deleteImage'])->name('settings.images.delete');
    Route::post('/settings/mappings', [App\Http\Controllers\SettingController::class, 'updateMapping'])->name('settings.mappings.update');
    Route::delete('/settings/mappings/{mapping}', [App\Http\Controllers\SettingController::class, 'deleteMapping'])->name('settings.mappings.delete');
    Route::post('/settings/custom-messages', [App\Http\Controllers\SettingController::class, 'storeCustomMessage'])->name('settings.custom-messages.store');
    Route::put('/settings/custom-messages/{customMessage}', [App\Http\Controllers\SettingController::class, 'updateCustomMessage'])->name('settings.custom-messages.update');
    Route::delete('/settings/custom-messages/{customMessage}', [App\Http\Controllers\SettingController::class, 'deleteCustomMessage'])->name('settings.custom-messages.delete');
    Route::get('/api/reminders', [QuotationController::class, 'getReminders'])->name('api.reminders');

    // Messages Management
    Route::resource('messages', App\Http\Controllers\MessageController::class);
    Route::get('/messages-hidden', [App\Http\Controllers\MessageController::class, 'hidden'])->name('messages.hidden');
    Route::post('/messages/{message}/send', [App\Http\Controllers\MessageController::class, 'send'])->name('messages.send');
    Route::post('/messages/{message}/duplicate', [App\Http\Controllers\MessageController::class, 'duplicate'])->name('messages.duplicate');
    Route::post('/messages/{message}/hide', [App\Http\Controllers\MessageController::class, 'hide'])->name('messages.hide');
    Route::post('/messages/{message}/unhide', [App\Http\Controllers\MessageController::class, 'unhide'])->name('messages.unhide');
    Route::get('/messages-export-pdf', [App\Http\Controllers\MessageController::class, 'exportPDF'])->name('messages.export.pdf');
    Route::post('/messages/whatsapp/log', [App\Http\Controllers\MessageController::class, 'logWhatsAppSend'])->name('messages.whatsapp.log');
    Route::post('/messages/predefined/{predefined}/favorite', [App\Http\Controllers\MessageController::class, 'toggleFavorite'])->name('messages.predefined.favorite');
    Route::post('/messages/flyers', [App\Http\Controllers\MessageController::class, 'storeFlyer'])->name('messages.flyers.store');
    Route::delete('/messages/flyers/{flyer}', [App\Http\Controllers\MessageController::class, 'deleteFlyer'])->name('messages.flyers.delete');
    Route::get('/api/messages/recipients', [App\Http\Controllers\MessageController::class, 'getRecipients'])->name('api.messages.recipients');
    Route::get('/api/messages/recent', [App\Http\Controllers\MessageController::class, 'getRecentMessages'])->name('api.messages.recent');

    // Soporte WhatsApp (modo directo por navegador)
    Route::get('/soporte', [App\Http\Controllers\SoporteController::class, 'index'])->name('soporte.index');
    Route::post('/soporte/messages', [App\Http\Controllers\SoporteController::class, 'storeMessage'])->name('soporte.messages.store');
    Route::delete('/soporte/messages/{id}', [App\Http\Controllers\SoporteController::class, 'deleteMessage'])->name('soporte.messages.delete');
    Route::get('/soporte/wa/qr', [App\Http\Controllers\SoporteController::class, 'qr'])->name('soporte.wa.qr');
    Route::get('/soporte/wa/status', [App\Http\Controllers\SoporteController::class, 'status'])->name('soporte.wa.status');
    Route::get('/soporte/wa/chats', [App\Http\Controllers\SoporteController::class, 'chats'])->name('soporte.wa.chats');
    Route::post('/soporte/wa/send', [App\Http\Controllers\SoporteController::class, 'send'])->name('soporte.wa.send');
    Route::post('/soporte/wa/disconnect', [App\Http\Controllers\SoporteController::class, 'disconnect'])->name('soporte.wa.disconnect');

});

require __DIR__ . '/auth.php';
