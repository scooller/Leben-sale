<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $recordId = $this->route('record');

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'min:4', 'max:32', 'unique:short_links,slug,'.$recordId],
            'destination_url' => ['required', 'url', 'max:2048'],
            'status' => ['required', 'in:active,disabled,expired'],
            'tag_manager_id' => ['nullable', 'string', 'regex:/^GTM-[A-Z0-9]+$/'],
            'expires_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
