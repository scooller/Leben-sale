<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdvisorWhatsappRedirectController extends Controller
{
    public function __invoke(Request $request, Asesor $asesor): View|RedirectResponse
    {
        if (! $asesor->is_active) {
            abort(404);
        }

        $phone = $this->sanitizePhone($asesor->whatsapp_owner);

        if ($phone === '') {
            abort(404);
        }

        $message = $this->resolveMessage($request, $asesor);
        $destinationUrl = sprintf('https://wa.me/%s?text=%s', $phone, rawurlencode($message));
        $tagManagerId = trim((string) SiteSetting::get('tag_manager_id', ''));

        if ($tagManagerId === '') {
            return redirect()->away($destinationUrl);
        }

        return view('whatsapp-links.redirect', [
            'destinationUrl' => $destinationUrl,
            'tagManagerId' => $tagManagerId,
            'redirectDelayMs' => 500,
            'eventData' => [
                'event' => 'wa_link',
                'action' => 'advisor_cta_click',
                'advisor_id' => $asesor->id,
                'advisor_name' => $asesor->full_name,
                'advisor_email' => $asesor->email,
                'plant_id' => $request->query('plant_id'),
                'plant_name' => $request->query('plant_name'),
                'project_name' => $request->query('project_name'),
                'source' => $request->query('source', 'advisor_whatsapp_redirect'),
                'destination' => $destinationUrl,
                'utm_source' => $request->query('utm_source'),
                'utm_medium' => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'utm_term' => $request->query('utm_term'),
                'utm_content' => $request->query('utm_content'),
                'utm_site' => $request->query('utm_site'),
                'redirected_at' => now()->toISOString(),
            ],
        ]);
    }

    private function sanitizePhone(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) ($value ?? '')) ?: '';
    }

    private function resolveMessage(Request $request, Asesor $asesor): string
    {
        $requestedMessage = trim((string) $request->query('text', ''));

        if ($requestedMessage !== '') {
            return $requestedMessage;
        }

        $contactName = trim((string) ($asesor->first_name ?: $asesor->full_name));

        if ($contactName === '') {
            $contactName = 'asesor';
        }

        return sprintf('Hola %s', $contactName);
    }
}
