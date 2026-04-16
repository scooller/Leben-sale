<?php

namespace Tests\Unit\FinMail;

use App\Support\FinMail\HtmlCodeEditor;
use Filament\Forms\Components\Textarea;
use PHPUnit\Framework\TestCase;

class HtmlCodeEditorTest extends TestCase
{
    public function test_it_returns_textarea_component_for_body_field(): void
    {
        $editor = new HtmlCodeEditor;

        $component = $editor->make('body');

        $this->assertInstanceOf(Textarea::class, $component);
        $this->assertSame('body', $component->getName());
    }

    public function test_it_exposes_editor_name(): void
    {
        $editor = new HtmlCodeEditor;

        $this->assertSame('html-code', $editor->name());
    }
}
