<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionRequest extends FormRequest
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
        return [
            'service_order_id' => ['required', 'exists:service_orders,id', 'unique:inspections,service_order_id'],
            'customer_complaint' => ['nullable', 'string'],
            'mechanic_notes' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
        ];
    }
}
