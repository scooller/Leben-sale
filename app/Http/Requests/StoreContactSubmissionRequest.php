<?php

namespace App\Http\Requests;

use App\Models\SiteSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreContactSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'fields' => ['required', 'array'],
        ];

        foreach ($this->configuredFields() as $field) {
            $key = Str::of((string) ($field['key'] ?? ''))->trim()->toString();

            if ($key === '') {
                continue;
            }

            $type = (string) ($field['type'] ?? 'text');
            $required = (bool) ($field['required'] ?? false);

            $fieldRules = [$required ? 'required' : 'nullable'];

            if ($type === 'email') {
                $fieldRules[] = 'string';
                $fieldRules[] = 'email';
                $fieldRules[] = 'max:255';
            } elseif ($type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($type === 'textarea') {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:5000';
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:255';
            }

            $rules["fields.{$key}"] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function configuredFields(): array
    {
        $settings = SiteSetting::current();
        $fields = $settings->contact_form_fields;

        if (! is_array($fields) || $fields === []) {
            return [
                ['key' => 'name', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'type' => 'email', 'required' => true],
                ['key' => 'message', 'type' => 'textarea', 'required' => true],
            ];
        }

        return $fields;
    }
}
