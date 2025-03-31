<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\PhoneValidationService;
use App\Exceptions\PhoneValidationException;

class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(PhoneValidationService::class);
        $result = $service->validate($value);

        if (!$result['valid']) {
            $fail('The :attribute number is not valid.');
        }
    }
}