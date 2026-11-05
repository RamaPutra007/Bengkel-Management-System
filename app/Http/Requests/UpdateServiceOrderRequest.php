<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('ADMIN');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'mechanic_id' => ['nullable', 'exists:mechanics,id'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];

        if ($this->input('status') === 'completed') {
            $rules['items'] = ['required', 'array', 'min:1'];
        } else {
            $rules['items'] = ['nullable', 'array'];
        }

        $rules['items.*.type'] = ['required_with:items', 'in:service,sparepart'];
        $rules['items.*.item_id'] = ['required_with:items', 'integer'];
        $rules['items.*.quantity'] = ['required_with:items', 'integer', 'min:1'];
        $rules['items.*.price'] = ['required_with:items', 'numeric', 'min:0'];

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Ketika status pengerjaan selesai, wajib menambahkan minimal 1 item (layanan jasa atau suku cadang).',
            'items.min' => 'Ketika status pengerjaan selesai, wajib menambahkan minimal 1 item (layanan jasa atau suku cadang).',
        ];
    }
}
