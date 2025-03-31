<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'unique:companies,name'],
            'email' => ['required', 'string', 'email', 'unique:companies,email'],
            'phone' => ['required', 'string', 'unique:companies,phone', new ValidPhoneNumber],
            'country_id' => ['required', 'exists:countries,id'],
            'industry_id' => ['required', 'exists:industries,id']
        ];
    }
}
