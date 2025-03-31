<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', new ValidPhoneNumber],
            'message' => ['required', 'string', 'max:1000'],
            'company_id' => ['required', 'exists:companies,id'],
        ];
    }
}
