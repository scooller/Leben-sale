<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Payments\Support\ManualPaymentActionSupport;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadManualProof')
                ->label('Descargar Comprobante')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => ManualPaymentActionSupport::hasManualProof($this->getRecord()))
                ->action(function () {
                    /** @var Payment $record */
                    $record = $this->getRecord();
                    $path = ManualPaymentActionSupport::manualProofPath($record);

                    if (! $path) {
                        Notification::make()
                            ->danger()
                            ->title('No hay comprobante asociado.')
                            ->send();

                        return null;
                    }

                    return Storage::download($path, ManualPaymentActionSupport::manualProofName($record));
                }),
            Action::make('approveManualPayment')
                ->label('Aprobar Pago Manual')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => ManualPaymentActionSupport::isManualPendingApproval($this->getRecord()))
                ->action(function (): void {
                    /** @var Payment $record */
                    $record = $this->getRecord();
                    $approved = ManualPaymentActionSupport::approve($record, Auth::id());

                    if ($approved) {
                        Notification::make()->success()->title('Pago manual aprobado.')->send();

                        return;
                    }

                    Notification::make()->danger()->title('No se pudo aprobar el pago manual.')->send();
                }),
            Action::make('rejectManualPayment')
                ->label('Rechazar Pago Manual')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('reason')
                        ->label('Motivo de rechazo')
                        ->maxLength(500)
                        ->required(),
                ])
                ->visible(fn (): bool => ManualPaymentActionSupport::isManualPendingApproval($this->getRecord()))
                ->action(function (array $data): void {
                    /** @var Payment $record */
                    $record = $this->getRecord();
                    $rejected = ManualPaymentActionSupport::reject(
                        payment: $record,
                        reason: (string) ($data['reason'] ?? 'Pago manual rechazado por administracion'),
                        rejectedBy: Auth::id(),
                    );

                    if ($rejected) {
                        Notification::make()->success()->title('Pago manual rechazado.')->send();

                        return;
                    }

                    Notification::make()->danger()->title('No se pudo rechazar el pago manual.')->send();
                }),
            EditAction::make(),
        ];
    }
}
