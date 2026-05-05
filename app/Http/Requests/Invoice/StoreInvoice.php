<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoice extends FormRequest
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
    public function rules()
    {
        return [
            'amountTo_pay' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'referencie' => 'required|numeric',
            'payment_status' => 'required|string|in:pendent,paid,faild',
            'payment_method' => 'nullable|string|in:cash,card,undefined',
        ];
    }
}
