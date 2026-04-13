<?php

namespace App\Filament\Resources\ContactSubmissions\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissions\ContactSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContactSubmission extends EditRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
