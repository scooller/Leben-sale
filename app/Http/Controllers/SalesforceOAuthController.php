<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class SalesforceOAuthController extends Controller
{
    /**
     * Inicia el flujo OAuth de Salesforce (WebServer).
     * Forrest::authenticate() construye la URL y redirige directamente a Salesforce.
     */
    public function connect(): RedirectResponse
    {
        Log::info('Salesforce OAuth: Iniciando flujo de conexión');

        // En WebServer flow, authenticate() retorna un RedirectResponse hacia Salesforce
        return Forrest::authenticate();
    }

    /**
     * Callback de Salesforce (después del login).
     * Forrest::callback() intercambia el código por token y lo almacena.
     */
    public function callback(): RedirectResponse
    {
        $error = request('error');
        $errorDescription = request('error_description');

        if ($error) {
            Log::warning('Salesforce OAuth: Error en callback', [
                'error' => $error,
                'error_description' => $errorDescription,
            ]);

            return redirect('/admin/site-settings')
                ->withErrors(['salesforce' => "Salesforce: {$errorDescription}"]);
        }

        try {
            // Forrest intercambia el código por token y lo guarda en cache
            Forrest::callback();

            Log::info('Salesforce OAuth: Autenticación completada exitosamente');

            // Guardar en cache por 5 minutos para que la notificación se muestre una sola vez
            \Illuminate\Support\Facades\Cache::put('salesforce_oauth_just_connected', true, now()->addMinutes(5));

            return redirect('/admin/site-settings');
        } catch (\Throwable $e) {
            Log::error('Salesforce OAuth: Error en callback', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/admin/site-settings')
                ->withErrors(['salesforce' => 'Error al procesar autorización: ' . $e->getMessage()]);
        }
    }
}
