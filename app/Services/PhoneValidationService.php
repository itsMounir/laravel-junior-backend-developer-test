<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Exceptions\PhoneValidationException;

class PhoneValidationService
{
    protected string $apiKey;
    protected string $endpoint = 'https://phonevalidation.abstractapi.com/v1/';
    protected int $cacheDuration;

    public function __construct()
    {
        $this->apiKey = config('services.abstract.phone_validation_key');
        $this->cacheDuration = config('services.abstract.phone_validation_cache_duration');
    }

    public function validate(string $phoneNumber): array
    {
        try {
            // Create a unique cache key for this phone number
            $cacheKey = "phone_validation_{$phoneNumber}";

            // Check if we have cached results for this number
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // If not in cache, make API call with retry mechanism
            $response = Http::timeout(10)
                ->retry(3, 100)  // Retry 3 times with 100ms delay
                ->get($this->endpoint, [
                    'api_key' => $this->apiKey,
                    'phone' => $phoneNumber
                ]);

            // Handle API errors
            if ($response->failed()) {
                throw new PhoneValidationException(
                    $this->getErrorMessage($response->status(), $response->body()),
                    $response->status()
                );
            }

            $data = $response->json();

            // Check if the number is valid
            if (!$data['valid']) {
                throw new PhoneValidationException(
                    $this->getInvalidNumberMessage($data),
                    $response->status()
                );
            }

            // Store valid result in cache for future use
            Cache::put($cacheKey, $data, $this->cacheDuration);

            return $data;

        } catch (Exception $e) {
            throw new PhoneValidationException(
                $this->getErrorMessage($e->getCode(), $e->getMessage()),
                $e->getCode()
            );
        }
    }

    /**
     * Formats a phone number into a consistent, readable format
     * Example: "1234567890" -> "(123) 456-7890"
     * Example: "963999999999" -> "(123) 456-678-890"
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters (spaces, dashes, etc.)
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Format 10-digit numbers into (XXX) XXX-XXXX format
        if (strlen($number) === 10) {
            return sprintf(
                '(%s) %s-%s',
                substr($number, 0, 3),    // First 3 digits
                substr($number, 3, 3),    // Next 3 digits
                substr($number, 6, 4)     // Last 4 digits
            );
        }

        // Format 12-digit numbers into (XXX) XXX-XXX-XXX format
        if (strlen($number) === 12) {
            return sprintf(
                '(%s) %s-%s-%s',
                substr($number, 0, 3),    // First 3 digits
                substr($number, 3, 3),    // Next 3 digits
                substr($number, 6, 3),    // Next 3 digits
                substr($number, 9, 3)     // Last 3 digits
            );
        }

        // Return original number if it doesn't match expected format
        return $phoneNumber;
    }

    /**
     * Get user-friendly error messages based on API status codes
     */
    protected function getErrorMessage(int $status, string $body): string
    {
        return match ($status) {
            401 => 'Invalid API key. Please check your configuration.',
            403 => 'API access denied. Please check your subscription.',
            429 => 'Too many requests. Please try again later.',
            default => "Phone validation API failed: {$body}",
        };
    }

    /**
     * Extract error message from API response data
     */
    protected function getInvalidNumberMessage(array $data): string
    {
        if (isset($data['error'])) {
            return $data['error']['message'] ?? 'Invalid phone number format';
        }

        return 'Invalid phone number format';
    }
}
