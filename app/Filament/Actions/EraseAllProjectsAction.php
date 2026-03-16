<?php

namespace App\Filament\Actions;

use App\Models\Proyecto;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EraseAllProjectsAction
{
    public static function make(): Action
    {
        return Action::make('erase_all_proyectos')
            ->label('Borrar Todos')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('⚠️ Borrar todas las plantas')
            ->modalDescription('¿Estás seguro de que deseas eliminar todos los proyectos? Esta acción no se puede deshacer.')
            ->modalSubmitActionLabel('Sí, borrar todos')
            ->modalCancelActionLabel('Cancelar')
            ->action(function () {
                try {
                    $count = Proyecto::count();

                    // Deshabilitar FK checks para poder truncate
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    Proyecto::truncate();
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                    Notification::make()
                        ->title('✅ Éxito')
                        ->body("Se eliminaron {$count} proyectos correctamente.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    // Asegurar que FK checks estén habilitadas en caso de error
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                    Notification::make()
                        ->title('❌ Error')
                        ->body('Error al borrar proyectos: '.$e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
