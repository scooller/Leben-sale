<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\Qr\Facades\Qr;

class ShowQrCodeAction
{
    public static function make(string|callable $urlResolver, ?string $name = 'showQrCode'): Action
    {
        return Action::make($name)
            ->label('Ver QR')
            ->icon('heroicon-o-qr-code')
            ->color('gray')
            ->modalHeading('Codigo QR')
            ->modalDescription('Escanea o copia la URL asociada a este enlace.')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->modalContent(function (Model $record) use ($urlResolver): View {
                $url = value($urlResolver, $record);

                return view('filament.actions.show-qr-code', [
                    'url' => $url,
                    'qrSvg' => Qr::render(data: $url),
                ]);
            })
            ->action(static fn (): null => null);
    }
}
