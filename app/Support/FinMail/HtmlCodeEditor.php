<?php

namespace App\Support\FinMail;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Component;
use FinityLabs\FinMail\Contracts\EditorContract;

class HtmlCodeEditor implements EditorContract
{
    public function make(string $fieldName): Component
    {
        return Textarea::make($fieldName)
            ->rows(20)
            ->extraInputAttributes([
                'style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;',
                'spellcheck' => 'false',
            ])
            ->columnSpanFull();
    }

    public function name(): string
    {
        return 'html-code';
    }
}
