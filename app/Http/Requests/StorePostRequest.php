<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Post::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'overview' => 'required|string',

            // Just to validate the array of packages that will come from the form
            'packages' => 'nullable|array',
            'packages.*.type' => 'required|string',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.capacity' => 'nullable|integer|min:1',
            'packages.*.description' => 'nullable|string',
        ];
    }
}
