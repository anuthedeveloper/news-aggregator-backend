<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePreferencesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            $allowedSources = config('news.sources');

            return [
                'categories' => 'nullable|array',
                'categories.*' => 'nullable|string',
                'sources' => 'nullable|array',
                'sources.*' => [
                    'string',
                    Rule::in($allowedSources),
                ],
                'authors' => 'nullable|array',
                'authors.*' => 'nullable|string',
            ];
      
    }
}
