<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Omniphx\Forrest\Facades\Forrest;

class SalesforceOAuthController extends Controller
{
    /**
     * Inicia el flujo OAuth de Salesforce (WebServer).
     * Redirige al usuario a Salesforce para login y autorización.
     */
    public function connect(): RedirectResponse
    {
        try {
            // Forrest maneja automáticamente el flujo WebServer
            // Redirige a Salesforce con el callback URL
            $loginUrl = Forrest::getLoginUrl();

            Log::info('Salesforce OAuth: Iniciando flujo de conexión', [
                'login_url' => $loginUrl,
            ]);

            return redirect($loginUrl);
        } catch (\Throwable $e) {
            Log::error('Salesforce OAuth: Error al iniciar conexión', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo conectar con Salesforce: ' . $e->getMessage());
        }
    }

    /**
     * Callback de Salesforce (después del login).
     * Salesforce redirige aquí con el código de autorización.
     */
    public function callback(): RedirectResponse
    {
        try {
            $code = request('code');
            $state = request('state');
            $error = request('error');
            $errorDescription = request('error_description');

            if ($error) {
                Log::warning('Salesforce OAuth: Error en callback', [
                    'error' => $error,
                    'error_description' => $errorDescription,
                ]);

                return redirect('/admin/site-settings')
                    ->with('error', "Salesforce: {$errorDescription}");
            }

            if (! $code) {
                Log::error('Salesforce OAuth: Código de autorización no recibido');

                return redirect('/admin/site-settings')
                    ->with('error', 'No se recibió código de autorización de Salesforce');
            }

            // Forrest maneja automáticamente el intercambio del código por token
            // Llamar a authenticate() fuerza el intercambio y almacenamiento del token
            Forrest::authenticate();

            Log::info('Salesforce OAuth: Autenticación completada exitosamente', [
                'instance_url' => Forrest::getInstanceURL(),
            ]);

            return redirect('/admin/site-settings')
                ->with('success', 'Conexión con Salesforce establecida correctamente');
        } catch (\Throwable $e) {
            Log::error('Salesforce OAuth: Error en callback', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect('/admin/site-settings')
                ->with('error', 'Error al procesar la autorización: ' . $e->getMessage());
        }
    }
}
