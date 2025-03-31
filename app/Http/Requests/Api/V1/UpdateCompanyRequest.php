<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
            'name' => ['string', Rule::unique('companies','name')->ignore($this->company->id)],
            'email' => ['string', 'email', Rule::unique('companies','email')->ignore($this->company->id)],
            'phone' => [Rule::unique('companies','phone')->ignore($this->company->id), new ValidPhoneNumber],
            'country_id' => ['exists:countries,id'],
            'industry_id' => ['exists:industries,id']
        ];
    }
}
